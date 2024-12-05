package repositories

import (
	"context"
	"database/sql"
	"errors"
	"time"

	"github.com/uptrace/bun"

	"github.com/artim-ctrl/finance/internal/database/postgres"
)

type Repository struct {
	db *postgres.Conn
}

func NewRepository(db *postgres.Conn) *Repository {
	return &Repository{
		db: db,
	}
}

func (r *Repository) CreateCategory(ctx context.Context, c *IncomeCategory) error {
	_, err := r.db.NewInsert().Model(c).Exec(ctx)

	return err
}

func (r *Repository) CreateIncome(ctx context.Context, i *Income) error {
	_, err := r.db.NewInsert().Model(i).Exec(ctx)

	return err
}

func (r *Repository) GetByDate(ctx context.Context, userID int64, date time.Time) ([]IncomeCategory, error) {
	start := time.Date(date.Year(), date.Month(), 1, 0, 0, 0, 0, time.UTC)
	end := time.Date(date.Year(), date.Month()+1, 1, 0, 0, 0, 0, time.UTC).Add(-time.Nanosecond)

	categories := make([]IncomeCategory, 0)
	err := r.db.NewSelect().Model(&categories).
		Relation("Incomes", func(query *bun.SelectQuery) *bun.SelectQuery {
			return query.
				Where("i.date BETWEEN ? AND ?", start, end).
				Where("i.deleted_at IS NULL")
		}).
		Where("ic.deleted_at IS NULL").
		Where("ic.user_id = ?", userID).
		Scan(ctx)
	if errors.Is(err, sql.ErrNoRows) {
		return categories, nil
	} else if err != nil {
		return nil, err
	}

	return categories, nil
}
