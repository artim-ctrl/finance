package validator

import (
	"reflect"
	"strings"

	"github.com/go-playground/locales/en"
	ut "github.com/go-playground/universal-translator"
	"github.com/go-playground/validator/v10"
	en2 "github.com/go-playground/validator/v10/translations/en"
)

type Validator struct {
	validate *validator.Validate
	trans    ut.Translator
}

func New() *Validator {
	eng := en.New()
	uni := ut.New(eng, eng)
	trans, _ := uni.GetTranslator("en")

	v := validator.New()
	_ = en2.RegisterDefaultTranslations(v, trans)

	return &Validator{
		validate: v,
		trans:    trans,
	}
}

func (v *Validator) ValidateStruct(s interface{}) map[string][]string {
	err := v.validate.Struct(s)
	if err == nil {
		return nil
	}

	errs := make(map[string][]string)
	typ := reflect.TypeOf(s)
	if typ.Kind() == reflect.Ptr {
		typ = typ.Elem()
	}

	for _, e := range err.(validator.ValidationErrors) {
		field, ok := typ.FieldByName(e.StructField())
		if !ok {
			errs[e.Field()] = []string{e.Translate(v.trans)}
			continue
		}

		jsonTag := field.Tag.Get("json")
		if jsonTag == "" {
			jsonTag = e.Field()
		} else if idx := strings.Index(jsonTag, ","); idx != -1 {
			jsonTag = jsonTag[:idx]
		}

		errs[jsonTag] = []string{e.Translate(v.trans)}
	}

	return errs
}
