package incomes

import (
	"go.uber.org/fx"

	"github.com/artim-ctrl/finance/internal/incomes/handlers"
	"github.com/artim-ctrl/finance/internal/incomes/repositories"
)

var Module = fx.Module("incomes", fx.Provide(
	repositories.NewRepository,
	handlers.NewHandler,
))
