package logger

import (
	"go.uber.org/zap"
	"go.uber.org/zap/zapcore"

	"github.com/artim-ctrl/finance/internal/config"
	"github.com/artim-ctrl/finance/internal/environment"
)

func New(env environment.Variables, c config.Config) (*zap.Logger, error) {
	var cfg zap.Config
	if c.Logger.Encoding == "console" {
		cfg = zap.NewDevelopmentConfig()
		cfg.EncoderConfig.EncodeLevel = zapcore.CapitalColorLevelEncoder
	} else {
		cfg = zap.NewProductionConfig()
		cfg.DisableCaller = !c.Logger.EnableCaller
		cfg.Sampling.Initial = c.Logger.Sampling.Initial
		cfg.Sampling.Thereafter = c.Logger.Sampling.Thereafter
		cfg.EncoderConfig.EncodeTime = zapcore.ISO8601TimeEncoder
	}
	cfg.DisableStacktrace = true
	cfg.Encoding = c.Logger.Encoding
	cfg.OutputPaths = []string{"stderr"}

	var lvl zapcore.Level
	err := lvl.UnmarshalText([]byte(c.Logger.Level))
	if err != nil {
		return nil, err
	}
	cfg.Level.SetLevel(lvl)

	var logger *zap.Logger
	logger, err = cfg.Build()
	if err != nil {
		return nil, err
	}

	return logger.WithOptions(
		zap.Fields(
			zap.String("service", "finance"),
			zap.String("env", env.Env),
		),
	), nil
}
