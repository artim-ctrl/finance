package repositories

import (
	"context"

	"github.com/artim-ctrl/finance/internal/database/postgres"
	"github.com/artim-ctrl/finance/internal/models"
)

type Repository struct {
	db *postgres.Conn
}

func NewRepository(db *postgres.Conn) *Repository {
	return &Repository{db: db}
}

func (r *Repository) CreateUser(ctx context.Context, user *models.User) error {
	_, err := r.db.NewInsert().Model(user).Exec(ctx)

	return err
}

func (r *Repository) CreateCurrency(ctx context.Context, currency *models.UserCurrency) error {
	_, err := r.db.NewInsert().Model(currency).Exec(ctx)

	return err
}

func (r *Repository) UpdateCurrency(ctx context.Context, currency *models.UserCurrency) error {
	_, err := r.db.NewUpdate().Model(currency).
		Column("currency").
		WherePK().
		Exec(ctx)

	return err
}

func (r *Repository) GetUserByEmail(ctx context.Context, email string) (*models.User, error) {
	user := &models.User{}
	err := r.db.NewSelect().Model(user).
		Relation("Currency").
		Where("u.email = ?", email).
		Where("u.deleted_at IS NULL").
		Scan(ctx)
	if err != nil {
		return nil, err
	}

	return user, err
}

func (r *Repository) GetActiveUserByID(ctx context.Context, userID int64) (*models.User, error) {
	user := &models.User{}
	err := r.db.NewSelect().Model(user).
		Relation("Currency").
		Where("u.id = ?", userID).
		Where("u.deleted_at IS NULL").
		Scan(ctx)
	if err != nil {
		return nil, err
	}

	return user, err
}
