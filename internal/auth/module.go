package auth

import (
	"go.uber.org/fx"

	"github.com/artim-ctrl/finance/internal/auth/handlers"
	"github.com/artim-ctrl/finance/internal/auth/repositories"
	"github.com/artim-ctrl/finance/internal/auth/token_manager"
)

var Module = fx.Module("auth", fx.Provide(
	repositories.NewRepository,
	token_manager.NewTokenManager,
	handlers.NewHandler,
))
