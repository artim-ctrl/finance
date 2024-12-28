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
	Date           string  `bun:"date" json:"date"`
	CurrentAmount  float64 `bun:"current_amount" json:"currentAmount"`
	PreviousAmount float64 `bun:"previous_amount" json:"previousAmount"`
}

func (r *Repository) Get(ctx context.Context, from, to, fromPrev, toPrev time.Time) ([]ExpenseData, error) {
	var expenses []ExpenseData
	err := r.db.NewSelect().
		With("current_dates", r.db.NewSelect().
			ColumnExpr("DATE(GENERATE_SERIES(?, ?, INTERVAL '1' DAY)) AS date", from.Format(time.DateOnly), to.Format(time.DateOnly))).
		With("current", r.db.NewSelect().
			ColumnExpr("cd.date, ROW_NUMBER() OVER (ORDER BY cd.date) AS num, SUM(e.amount) AS amount").
			TableExpr("current_dates cd").
			Join("LEFT JOIN expenses e ON e.date = cd.date").
			Group("cd.date")).
		With("previous_dates", r.db.NewSelect().
			ColumnExpr("DATE(GENERATE_SERIES(?, ?, INTERVAL '1' DAY)) AS date", fromPrev.Format(time.DateOnly), toPrev.Format(time.DateOnly))).
		With("previous", r.db.NewSelect().
			ColumnExpr("pd.date, ROW_NUMBER() OVER (ORDER BY pd.date) AS num, SUM(e.amount) AS amount").
			TableExpr("previous_dates pd").
			Join("LEFT JOIN expenses e ON e.date = pd.date").
			Group("pd.date")).
		TableExpr("current c").
		ColumnExpr("c.date, COALESCE(c.amount, 0) AS current_amount, COALESCE(p.amount, 0) AS previous_amount").
		Join("LEFT JOIN previous p ON c.num = p.num").
		Scan(ctx, &expenses)
	if err != nil {
		return nil, err
	}

	return expenses, err
}
