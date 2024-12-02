package environment

import "flag"

const (
	Base = "base"
	Dev  = "dev"
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
