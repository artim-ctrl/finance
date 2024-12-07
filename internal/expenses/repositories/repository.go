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

func (r *Repository) CreateCategory(ctx context.Context, c *ExpenseCategory) error {
	_, err := r.db.NewInsert().Model(c).Exec(ctx)

	return err
}

func (r *Repository) CreateExpense(ctx context.Context, i *Expense) error {
	_, err := r.db.NewInsert().Model(i).Exec(ctx)

	return err
}

func (r *Repository) GetByDate(ctx context.Context, userID int64, date time.Time) ([]ExpenseCategory, error) {
	start := time.Date(date.Year(), date.Month(), 1, 0, 0, 0, 0, time.UTC)
	end := time.Date(date.Year(), date.Month()+1, 1, 0, 0, 0, 0, time.UTC).Add(-time.Nanosecond)

	categories := make([]ExpenseCategory, 0)
	err := r.db.NewSelect().Model(&categories).
		Relation("Expenses", func(query *bun.SelectQuery) *bun.SelectQuery {
			return query.
				Where("e.date BETWEEN ? AND ?", start, end).
				Where("e.deleted_at IS NULL")
		}).
		Relation("MonthlyExpensePlans", func(query *bun.SelectQuery) *bun.SelectQuery {
			return query.
				Where("mep.year = ? AND mep.month = ?", date.Year(), date.Month())
		}).
		Where("ec.deleted_at IS NULL").
		Where("ec.user_id = ?", userID).
		Scan(ctx)
	if errors.Is(err, sql.ErrNoRows) {
		return categories, nil
	} else if err != nil {
		return nil, err
	}

	return categories, nil
}

func (r *Repository) UpsertPlan(ctx context.Context, plan *MonthlyExpensePlan) error {
	_, err := r.db.NewInsert().Model(plan).
		On("CONFLICT (expense_category_id, year, month) DO UPDATE").
		Set("amount = EXCLUDED.amount, updated_at = CURRENT_TIMESTAMP").
		Exec(ctx)

	return err
}
