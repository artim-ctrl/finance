package servers

import (
	"go.uber.org/fx"

	"github.com/artim-ctrl/finance/internal/servers/http"
)

var Module = fx.Module("servers", fx.Provide(
	http.NewRouter,
	http.New,
	New,
))

type Servers struct {
	Http *http.Server
}

func New(http *http.Server) *Servers {
	return &Servers{
		Http: http,
	}
}
