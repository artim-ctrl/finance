package http

import (
	"github.com/gofiber/fiber/v2"

	htest "github.com/artim-ctrl/finance/internal/servers/http/handlers/test"
	mtest "github.com/artim-ctrl/finance/internal/servers/http/mappers/test"
)

type Router struct {
	handler *htest.Handler
	mapper  *mtest.Mapper
}

func NewRouter(handler *htest.Handler, mapper *mtest.Mapper) *Router {
	return &Router{
		handler: handler,
		mapper:  mapper,
	}
}

func (r *Router) Setup(app *fiber.App) {
	group := app.Group("/v1")

	group.Get("/test", r.mapper.Map, r.handler.Handle)
}
