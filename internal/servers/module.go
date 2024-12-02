package servers

import (
	"go.uber.org/fx"

	"github.com/artim-ctrl/finance/internal/servers/http"
	htest "github.com/artim-ctrl/finance/internal/servers/http/handlers/test"
	mtest "github.com/artim-ctrl/finance/internal/servers/http/mappers/test"
)

var Module = fx.Module("servers", fx.Provide(
	htest.New,
	mtest.New,
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
