package expenses

import (
	"go.uber.org/fx"

	"github.com/artim-ctrl/finance/internal/expenses/handlers"
	"github.com/artim-ctrl/finance/internal/expenses/repositories"
)

var Module = fx.Module("expenses", fx.Provide(
	repositories.NewRepository,
	handlers.NewHandler,
))
