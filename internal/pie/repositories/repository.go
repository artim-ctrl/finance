package repositories

import (
	"context"

	"github.com/artim-ctrl/finance/internal/database/postgres"
)

type Repository struct {
	db *postgres.Conn
}

func NewRepository(db *postgres.Conn) *Repository {
	return &Repository{db: db}
}

type ExpenseData struct {
	Category string  `bun:"category" json:"category"`
	Amount   float64 `bun:"amount" json:"amount"`
}

func (r *Repository) Get(ctx context.Context) ([]ExpenseData, error) {
	var expenses []ExpenseData

	err := r.db.NewSelect().
		ColumnExpr("ec.name AS category, SUM(expenses.amount) AS amount").
		Table("expenses").
		Join("INNER JOIN expense_categories ec ON expenses.expense_category_id = ec.id").
		Where("expenses.date >= CURRENT_DATE - INTERVAL '30 days'").
		Group("ec.name").
		Order("amount DESC").
		Scan(ctx, &expenses)
	if err != nil {
		return nil, err
	}

	return expenses, nil
}
