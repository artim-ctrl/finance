package repositories

import (
	"context"
	"time"

	"github.com/uptrace/bun"

	"github.com/artim-ctrl/finance/internal/database/postgres"
)

type Repository struct {
	db *postgres.Conn
}

func NewRepository(db *postgres.Conn) *Repository {
	return &Repository{db: db}
}

func (r *Repository) GetCategories(ctx context.Context, userId int64, from, to, fromPrev, toPrev time.Time) ([]ExpenseCategory, error) {
	var categories []ExpenseCategory
	err := r.db.NewSelect().
		ColumnExpr("DISTINCT ec.id, ec.name").
		Model((*ExpenseCategory)(nil)).
		Join("JOIN expenses e ON e.expense_category_id = ec.id").
		Where("ec.user_id = ?", userId).
		WhereGroup(" AND ", func(query *bun.SelectQuery) *bun.SelectQuery {
			return query.
				Where("e.date BETWEEN ? AND ?", from, to).
				WhereOr("e.date BETWEEN ? AND ?", fromPrev, toPrev)
		}).
		Scan(ctx, &categories)
	if err != nil {
		return nil, err
	}

	return categories, nil
}

type ExpenseData struct {
	Date           string  `bun:"date" json:"date"`
	CurrentAmount  float64 `bun:"current_amount" json:"currentAmount"`
	PreviousAmount float64 `bun:"previous_amount" json:"previousAmount"`
}

func (r *Repository) Get(
	ctx context.Context,
	categories []int64,
	userId int64,
	from,
	to,
	fromPrev,
	toPrev time.Time,
) ([]ExpenseData, error) {
	currentSub := r.db.NewSelect().
		ColumnExpr("cd.date, ROW_NUMBER() OVER (ORDER BY cd.date) AS num, SUM(e.amount) AS amount").
		TableExpr("current_dates cd").
		Join("LEFT JOIN expenses e ON e.date = cd.date").
		Join("LEFT JOIN expense_categories ec ON ec.id = e.expense_category_id").
		Where("ec.user_id = ?", userId).
		Group("cd.date")

	if len(categories) > 0 {
		currentSub = currentSub.
			Where("ec.id IN (?)", bun.In(categories))
	}

	previousSub := r.db.NewSelect().
		ColumnExpr("pd.date, ROW_NUMBER() OVER (ORDER BY pd.date) AS num, SUM(e.amount) AS amount").
		TableExpr("previous_dates pd").
		Join("LEFT JOIN expenses e ON e.date = pd.date").
		Join("LEFT JOIN expense_categories ec ON ec.id = e.expense_category_id").
		Where("ec.user_id = ?", userId).
		Group("pd.date")

	if len(categories) > 0 {
		previousSub = previousSub.
			Where("ec.id IN (?)", bun.In(categories))
	}

	var expenses []ExpenseData
	err := r.db.NewSelect().
		With("current_dates", r.db.NewSelect().
			ColumnExpr("DATE(GENERATE_SERIES(?, ?, INTERVAL '1' DAY)) AS date", from.Format(time.DateOnly), to.Format(time.DateOnly))).
		With("current", currentSub).
		With("previous_dates", r.db.NewSelect().
			ColumnExpr("DATE(GENERATE_SERIES(?, ?, INTERVAL '1' DAY)) AS date", fromPrev.Format(time.DateOnly), toPrev.Format(time.DateOnly))).
		With("previous", previousSub).
		TableExpr("current c").
		ColumnExpr("c.date, COALESCE(c.amount, 0) AS current_amount, COALESCE(p.amount, 0) AS previous_amount").
		Join("LEFT JOIN previous p ON c.num = p.num").
		Scan(ctx, &expenses)
	if err != nil {
		return nil, err
	}

	return expenses, err
}
