package database

import (
	"go.uber.org/fx"

	"github.com/artim-ctrl/finance/internal/database/postgres"
)

var Module = fx.Module("database", fx.Provide(
	postgres.New,
	postgres.NewMigrator,
))
