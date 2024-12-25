package repositories

import (
	"context"
	"time"

	"github.com/artim-ctrl/finance/internal/database/postgres"
)

type Repository struct {
	db *postgres.Conn
}

func NewRepository(db *postgres.Conn) *Repository {
	return &Repository{db: db}
}

type ExpenseData struct {
	ID       int64   `bun:"id" json:"id"`
	Category string  `bun:"category" json:"category"`
	Amount   float64 `bun:"amount" json:"amount"`
}

func (r *Repository) Get(ctx context.Context, from, to time.Time) ([]ExpenseData, error) {
	var expenses []ExpenseData

	err := r.db.NewSelect().
		ColumnExpr("ec.id as id, ec.name AS category, SUM(expenses.amount) AS amount").
		Table("expenses").
		Join("INNER JOIN expense_categories ec ON expenses.expense_category_id = ec.id").
		Where("expenses.date BETWEEN ? AND ?", from.Format(time.DateOnly), to.Format(time.DateOnly)).
		Group("ec.id").
		Order("amount DESC").
		Scan(ctx, &expenses)
	if err != nil {
		return nil, err
	}

	return expenses, nil
}
