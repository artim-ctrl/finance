package pie

import (
	"go.uber.org/fx"

	"github.com/artim-ctrl/finance/internal/pie/handlers"
	"github.com/artim-ctrl/finance/internal/pie/repositories"
)

var Module = fx.Module("charts", fx.Provide(
	repositories.NewRepository,
	handlers.NewHandler,
))
