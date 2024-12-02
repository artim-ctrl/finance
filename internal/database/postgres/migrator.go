package postgres

import (
	"context"
	"fmt"
	"os"

	"go.uber.org/zap"

	"github.com/uptrace/bun/migrate"
)

type Migrator struct {
	db       *Conn
	migrator *migrate.Migrator
	logger   *zap.Logger
}

func NewMigrator(db *Conn, logger *zap.Logger) *Migrator {
	logger = logger.With(zap.String("subsystem", "migrator"))

	migrations := migrate.NewMigrations()
	if err := migrations.Discover(os.DirFS("migrations")); err != nil {
		logger.Fatal("migrations discovery failed", zap.Error(err))
	}
	opts := []migrate.MigratorOption{
		migrate.WithTableName("migrations"),
		migrate.WithLocksTableName("migration_locks"),
	}

	migrator := migrate.NewMigrator(db.DB, migrations, opts...)

	return &Migrator{
		db:       db,
		migrator: migrator,
		logger:   logger,
	}
}

func (m *Migrator) Run(ctx context.Context) {
	var (
		group *migrate.MigrationGroup
		err   error
	)

	// create required tables if not exist
	if err = m.migrator.Init(ctx); err != nil {
		m.logger.Fatal("initialization failed", zap.Error(err))
	}

	// execute migrations
	group, err = m.migrator.Migrate(ctx)
	if err != nil {
		if err.Error() == "migrate: there are no migrations" {
			m.logger.Info("no migrations found")
			return
		}

		m.logger.Fatal("execution failed", zap.Error(err))
	}

	if group.IsZero() {
		m.logger.Info("migrations is up to date")
		return
	}

	m.logger.Info(fmt.Sprintf("migrated to version: %d", group.ID))
}
