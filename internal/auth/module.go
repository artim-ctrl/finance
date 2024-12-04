package auth

import (
	"go.uber.org/fx"

	"github.com/artim-ctrl/finance/internal/auth/handlers"
	"github.com/artim-ctrl/finance/internal/auth/repositories"
	"github.com/artim-ctrl/finance/internal/auth/tokens"
)

var Module = fx.Options(
	fx.Provide(repositories.NewRepository),
	fx.Provide(tokens.NewTokenManager),
	fx.Provide(handlers.NewHandler),
)
