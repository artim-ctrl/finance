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
	var env = flag.String("env", "dev", "environment")

	return Variables{
		Env: *env,
	}
}

func (v *Variables) IsProd() bool {
	return v.Env == Prod
}
