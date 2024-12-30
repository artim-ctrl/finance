package repositories

import "github.com/uptrace/bun"

type ExpenseCategory struct {
	bun.BaseModel `bun:"table:expense_categories,alias:ec"`

	ID   int64  `bun:"id" json:"id"`
	Name string `bun:"name" json:"name"`
}
