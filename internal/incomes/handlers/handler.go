package handlers

import (
	"github.com/artim-ctrl/finance/internal/incomes/repositories"
	"github.com/artim-ctrl/finance/internal/validator"
)

type Handler struct {
	repo      *repositories.Repository
	validator *validator.Validator
}

func NewHandler(
	repo *repositories.Repository,
	validator *validator.Validator,
) *Handler {
	return &Handler{
		repo:      repo,
		validator: validator,
	}
}
