package config

import (
	"errors"
	"os"
	"path"

	"go.uber.org/config"
	"go.uber.org/zap"

	"github.com/artim-ctrl/finance/internal/environment"
)

const configPath = "./config"

type Config struct {
	Database Database `yaml:"database"`
	Logger   Logger   `yaml:"logger"`
	Auth     Auth     `yaml:"auth"`
	Frontend Frontend `yaml:"frontend"`
}

type Database struct {
	Dsn   string `yaml:"dsn"`
	Debug bool   `yaml:"debug"`
}

type Logger struct {
	Level        string             `yaml:"level"`    // Log level. Possible values: `debug`, `info`, `warn`, `error`, `dpanic`, `panic`, `fatal`
	Encoding     string             `yaml:"encoding"` // If encoding is `console`: logger uses development config. Sampling is disabled.
	Tags         []string           `yaml:"tags"`
	Sampling     zap.SamplingConfig `yaml:"sampling"`  // Warn: initial: 0, thereafter: 0 - disables logging.
	UseDiode     bool               `yaml:"use_diode"` // Use github.com/propellerads/logdiode writer. Need for small amount of particular cases
	EnableCaller bool               `yaml:"enable_caller"`
}

type Auth struct {
	AccessSecretKey  string `yaml:"access_secret_key"`
	RefreshSecretKey string `yaml:"refresh_secret_key"`
}

type Frontend struct {
	BaseUrl string `yaml:"base_url"`
}

func New(env environment.Variables) (Config, error) {
	var (
		c   Config
		err error
	)

	if env.Env == environment.Base {
		return c, errors.New("'base' can not be environment")
	}

	opts := []config.YAMLOption{config.File(path.Join(configPath, "base.yml"))}

	envConfigPath := path.Join(configPath, env.Env+".yml")
	if _, err = os.Stat(envConfigPath); err != nil {
		return c, err
	}

	opts = append(opts, config.File(envConfigPath))

	var yaml *config.YAML

	if yaml, err = config.NewYAML(opts...); err != nil {
		return c, err
	}
	if err = yaml.Get("").Populate(&c); err != nil {
		return c, err
	}

	return c, nil
}
