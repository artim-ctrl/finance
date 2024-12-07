package repositories

import (
	"time"

	"github.com/uptrace/bun"
)

type ExpenseCategory struct {
	bun.BaseModel `bun:"table:expense_categories,alias:ec"`

	ID        int64      `bun:",pk,autoincrement" json:"id"`
	UserID    int64      `bun:"user_id" json:"user_id"`
	Name      string     `bun:"name" json:"name"`
	CreatedAt time.Time  `bun:"created_at,default:current_timestamp" json:"created_at"`
	UpdatedAt time.Time  `bun:"updated_at,default:current_timestamp" json:"updated_at"`
	DeletedAt *time.Time `bun:"deleted_at" json:"deleted_at"`

	Expenses            []Expense            `bun:"rel:has-many,join:id=expense_category_id" json:"expenses,omitempty"`
	MonthlyExpensePlans []MonthlyExpensePlan `bun:"rel:has-many,join:id=expense_category_id" json:"monthly_expense_plans,omitempty"`
}

type Expense struct {
	bun.BaseModel `bun:"table:expenses,alias:e"`

	ID                int64      `bun:",pk,autoincrement" json:"id"`
	ExpenseCategoryID int64      `bun:"expense_category_id" json:"-"`
	Date              time.Time  `bun:"date" json:"date"`
	Amount            float64    `bun:"amount" json:"amount"`
	Comment           *string    `bun:"comment" json:"comment"`
	CreatedAt         time.Time  `bun:"created_at,default:current_timestamp" json:"created_at"`
	UpdatedAt         time.Time  `bun:"updated_at,default:current_timestamp" json:"updated_at"`
	DeletedAt         *time.Time `bun:"deleted_at" json:"deleted_at"`

	ExpenseCategory ExpenseCategory `bun:"rel:belongs-to,join:expense_category_id=id" json:"expense_category"`
}

type MonthlyExpensePlan struct {
	bun.BaseModel `bun:"table:monthly_expense_plans,alias:mep"`

	ID                int64     `bun:",pk,autoincrement" json:"id"`
	ExpenseCategoryID int64     `bun:"expense_category_id" json:"expense_category_id"`
	Year              int       `bun:"year" json:"year"`
	Month             int       `bun:"month" json:"month"`
	Amount            float64   `bun:"amount" json:"amount"`
	CreatedAt         time.Time `bun:"created_at,default:current_timestamp" json:"created_at"`
	UpdatedAt         time.Time `bun:"updated_at,default:current_timestamp" json:"updated_at"`
}
