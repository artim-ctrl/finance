package repositories

import (
	"time"

	"github.com/uptrace/bun"
)

type User struct {
	bun.BaseModel `bun:"table:users,alias:u"`

	ID               int64      `bun:",pk,autoincrement" json:"id"`
	Name             string     `bun:"name" json:"name"`
	Email            string     `bun:"email" json:"email"`
	Password         string     `bun:"password" json:"-"`
	CreatedAt        time.Time  `bun:"created_at" json:"created_at"`
	EmailConfirmedAt *time.Time `bun:"email_confirmed_at" json:"email_confirmed_at"`
	UpdatedAt        time.Time  `bun:"updated_at" json:"updated_at"`
	DeletedAt        *time.Time `bun:"deleted_at,soft_delete" json:"deleted_at"`
}
