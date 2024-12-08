package models

import (
	"time"

	"github.com/uptrace/bun"
)

type User struct {
	bun.BaseModel `bun:"table:users,alias:u"`

	ID               int64      `bun:",pk,autoincrement" json:"-"`
	Name             string     `bun:"name" json:"name"`
	Email            string     `bun:"email" json:"email"`
	Password         string     `bun:"password" json:"-"`
	CreatedAt        time.Time  `bun:"created_at,default:current_timestamp" json:"-"`
	EmailConfirmedAt *time.Time `bun:"email_confirmed_at" json:"-"`
	UpdatedAt        time.Time  `bun:"updated_at,default:current_timestamp" json:"-"`
	DeletedAt        *time.Time `bun:"deleted_at,soft_delete" json:"-"`

	Currency UserCurrency `bun:"rel:has-one,join:id=user_id" json:"currency,omitempty"`
}

type UserCurrency struct {
	bun.BaseModel `bun:"table:user_currencies,alias:uc"`

	ID       int64  `bun:",pk,autoincrement" json:"-"`
	UserID   int64  `bun:"user_id" json:"-"`
	Currency string `bun:"currency" json:"currency"`
}
