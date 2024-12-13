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
	Date                  string  `bun:"date" json:"date"`
	Last30DaysExpense     float64 `bun:"last_30_days_expense" json:"last30Days"`
	Previous30DaysExpense float64 `bun:"previous_30_days_expense" json:"previous30Days"`
}

func (r *Repository) Get(ctx context.Context) ([]ExpenseData, error) {
	var expenses []ExpenseData
	err := r.db.NewSelect().
		With("last_30_days", r.db.NewSelect().
			ColumnExpr("date, SUM(amount) AS total_expense").
			Table("expenses").
			Where("date >= CURRENT_DATE - INTERVAL '30 days'").
			Where("date < CURRENT_DATE").
			Group("date")).
		With("previous_30_days", r.db.NewSelect().
			ColumnExpr("date, SUM(amount) AS total_expense").
			Table("expenses").
			Where("date >= CURRENT_DATE - INTERVAL '60 days'").
			Where("date < CURRENT_DATE - INTERVAL '30 days'").
			Group("date")).
		Table("last_30_days").
		Join("LEFT JOIN previous_30_days ON (last_30_days.date - interval '30' day) = previous_30_days.date").
		ColumnExpr("DATE(last_30_days.date) AS date").
		ColumnExpr("COALESCE(last_30_days.total_expense, 0) AS last_30_days_expense").
		ColumnExpr("COALESCE(previous_30_days.total_expense, 0) AS previous_30_days_expense").
		Order("last_30_days.date").
		Scan(ctx, &expenses)
	if err != nil {
		return nil, err
	}

	return expenses, err
}
