package repositories

import (
	"time"

	"github.com/uptrace/bun"
)

type IncomeCategory struct {
	bun.BaseModel `bun:"table:income_categories,alias:ic"`

	ID        int64      `bun:",pk,autoincrement" json:"id"`
	UserID    int64      `bun:"user_id" json:"user_id"`
	Name      string     `bun:"name" json:"name"`
	CreatedAt time.Time  `bun:"created_at,default:current_timestamp" json:"created_at"`
	UpdatedAt time.Time  `bun:"updated_at,default:current_timestamp" json:"updated_at"`
	DeletedAt *time.Time `bun:"deleted_at" json:"deleted_at"`

	Incomes []Income `bun:"rel:has-many,join:id=income_category_id" json:"incomes,omitempty"`
}

type Income struct {
	bun.BaseModel `bun:"table:incomes,alias:i"`

	ID               int64      `bun:",pk,autoincrement" json:"id"`
	IncomeCategoryID int64      `bun:"income_category_id" json:"-"`
	Date             time.Time  `bun:"date" json:"date"`
	Amount           float64    `bun:"amount" json:"amount"`
	Comment          *string    `bun:"comment" json:"comment"`
	CreatedAt        time.Time  `bun:"created_at,default:current_timestamp" json:"created_at"`
	UpdatedAt        time.Time  `bun:"updated_at,default:current_timestamp" json:"updated_at"`
	DeletedAt        *time.Time `bun:"deleted_at" json:"deleted_at"`

	IncomeCategory IncomeCategory `bun:"rel:belongs-to,join:income_category_id=id" json:"income_category"`
}
