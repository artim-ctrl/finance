package handlers

import (
	"github.com/artim-ctrl/finance/internal/auth/cookie_manager"
	"github.com/artim-ctrl/finance/internal/auth/repositories"
	"github.com/artim-ctrl/finance/internal/auth/token_manager"
)

type Handler struct {
	repo          *repositories.Repository
	cookieManager *cookie_manager.CookieManager
	tokenManager  *token_manager.TokenManager
}

func NewHandler(
	repo *repositories.Repository,
	cookieManager *cookie_manager.CookieManager,
	tokenManager *token_manager.TokenManager,
) *Handler {
	return &Handler{
		repo:          repo,
		cookieManager: cookieManager,
		tokenManager:  tokenManager,
	}
}
