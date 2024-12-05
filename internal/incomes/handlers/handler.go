package handlers

import (
	"github.com/artim-ctrl/finance/internal/incomes/repositories"
)

type Handler struct {
	repo *repositories.Repository
}

func NewHandler(repo *repositories.Repository) *Handler {
	return &Handler{
		repo: repo,
	}
}
