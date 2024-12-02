package postgres

import (
	"database/sql"
	"time"

	"github.com/uptrace/bun"
	"github.com/uptrace/bun/dialect/pgdialect"
	"github.com/uptrace/bun/driver/pgdriver"
	"github.com/uptrace/bun/extra/bundebug"
	"go.uber.org/zap"

	"github.com/artim-ctrl/finance/internal/config"
)

type Conn struct {
	*bun.DB
}

func New(config config.Config, logger *zap.Logger) *Conn {
	logger = logger.With(zap.String("module", "database"))

	dbConn := sql.OpenDB(pgdriver.NewConnector(pgdriver.WithDSN(config.Database.Dsn)))

	dbConn.SetMaxIdleConns(5)
	dbConn.SetConnMaxIdleTime(10 * time.Minute)
	dbConn.SetConnMaxLifetime(60 * time.Minute)

	if err := dbConn.Ping(); err != nil {
		logger.Fatal("ping failed", zap.Error(err))
	}

	db := bun.NewDB(dbConn, pgdialect.New())

	db.AddQueryHook(bundebug.NewQueryHook(bundebug.WithVerbose(config.Database.Debug)))

	return &Conn{DB: db}
}
