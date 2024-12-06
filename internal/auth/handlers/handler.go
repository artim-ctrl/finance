package handlers

import (
	"github.com/artim-ctrl/finance/internal/auth/cookie_manager"
	"github.com/artim-ctrl/finance/internal/auth/repositories"
	"github.com/artim-ctrl/finance/internal/auth/token_manager"
	"github.com/artim-ctrl/finance/internal/validator"
)

type Handler struct {
	repo          *repositories.Repository
	cookieManager *cookie_manager.CookieManager
	tokenManager  *token_manager.TokenManager
	validator     *validator.Validator
}

func NewHandler(
	repo *repositories.Repository,
	cookieManager *cookie_manager.CookieManager,
	tokenManager *token_manager.TokenManager,
	validator *validator.Validator,
) *Handler {
	return &Handler{
		repo:          repo,
		cookieManager: cookieManager,
		tokenManager:  tokenManager,
		validator:     validator,
	}
}
