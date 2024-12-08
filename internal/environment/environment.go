package environment

import "flag"

const (
	Base = "base"
	Dev  = "dev"
	Prod = "prod"
)

type Variables struct {
	Env string
}

func New() Variables {
	env := flag.String("env", "dev", "environment")

	flag.Parse()

	return Variables{
		Env: *env,
	}
}

func (v *Variables) IsProd() bool {
	return v.Env == Prod
}
