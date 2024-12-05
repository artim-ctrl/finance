package main

import (
	"context"
	"fmt"
	"os"
	"time"

	"go.uber.org/fx"
	"go.uber.org/fx/fxevent"
	"go.uber.org/zap"

	"github.com/artim-ctrl/finance/internal/auth"
	"github.com/artim-ctrl/finance/internal/config"
	"github.com/artim-ctrl/finance/internal/database"
	"github.com/artim-ctrl/finance/internal/database/postgres"
	"github.com/artim-ctrl/finance/internal/environment"
	"github.com/artim-ctrl/finance/internal/incomes"
	"github.com/artim-ctrl/finance/internal/logger"
	"github.com/artim-ctrl/finance/internal/servers"
)

var opts = []fx.Option{
	database.Module,
	auth.Module,
	incomes.Module,
	servers.Module,
	fx.Provide(
		environment.New,
		config.New,
		logger.New,
	),
	fx.Invoke(
		func(
			lc fx.Lifecycle,
			stop fx.Shutdowner,
			logger *zap.Logger,
			servers *servers.Servers,
			migrator *postgres.Migrator,
		) {
			startCtx, cancel := context.WithCancel(context.Background())

			lc.Append(
				fx.Hook{
					OnStart: func(_ context.Context) error {
						servers.Http.Start(startCtx)

						migrator.Run(startCtx)

						return nil
					},
					OnStop: func(stopCtx context.Context) error {
						servers.Http.Stop(stopCtx)

						cancel()

						_ = logger.Sync()

						return nil
					},
				},
			)
		},
	),
	fx.WithLogger(func(logger *zap.Logger) fxevent.Logger {
		return &fxevent.ZapLogger{Logger: logger.With(zap.String("subsystem", "fx"))}
	}),
	fx.StartTimeout(30 * time.Second),
	fx.StopTimeout(30 * time.Second),
}

func main() {
	app := fx.New(opts...)

	if app.Err() != nil {
		vis, err := fx.VisualizeError(app.Err())
		if err == nil {
			_, _ = fmt.Fprintln(os.Stderr, vis)
		}
		panic(app.Err())
	}

	app.Run()
}
