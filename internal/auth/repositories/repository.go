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

func (r *Repository) CreateUser(ctx context.Context, user *User) error {
	_, err := r.db.NewInsert().Model(user).Exec(ctx)

	return err
}

func (r *Repository) GetUserByEmail(ctx context.Context, email string) (*User, error) {
	user := &User{}
	err := r.db.NewSelect().Model(user).
		Where("u.email = ?", email).
		Where("u.deleted_at IS NULL").
		Scan(ctx)
	if err != nil {
		return nil, err
	}

	return user, err
}

func (r *Repository) GetActiveUserByID(ctx context.Context, userID int64) (*User, error) {
	user := &User{}
	err := r.db.NewSelect().Model(user).
		Where("u.id = ?", userID).
		Where("u.deleted_at IS NULL").
		Scan(ctx)
	if err != nil {
		return nil, err
	}

	return user, err
}
