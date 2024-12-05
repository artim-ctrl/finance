package auth

import (
	"go.uber.org/fx"

	"github.com/artim-ctrl/finance/internal/auth/cookie_manager"
	"github.com/artim-ctrl/finance/internal/auth/handlers"
	"github.com/artim-ctrl/finance/internal/auth/repositories"
	"github.com/artim-ctrl/finance/internal/auth/token_manager"
)

var Module = fx.Module("auth", fx.Provide(
	repositories.NewRepository,
	cookie_manager.NewCookieManager,
	token_manager.NewTokenManager,
	handlers.NewHandler,
))
