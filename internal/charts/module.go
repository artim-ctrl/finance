package charts

import (
	"go.uber.org/fx"

	"github.com/artim-ctrl/finance/internal/charts/handlers"
	"github.com/artim-ctrl/finance/internal/charts/repositories"
)

var Module = fx.Module("charts", fx.Provide(
	repositories.NewRepository,
	handlers.NewHandler,
))
