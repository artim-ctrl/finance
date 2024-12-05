package http

import (
	"github.com/gofiber/fiber/v2"

	ahandler "github.com/artim-ctrl/finance/internal/auth/handlers"
	ihandler "github.com/artim-ctrl/finance/internal/incomes/handlers"
)

type Router struct {
	authHandler    *ahandler.Handler
	incomesHandler *ihandler.Handler
}

func NewRouter(
	authHandler *ahandler.Handler,
	incomesHandler *ihandler.Handler,
) *Router {
	return &Router{
		authHandler:    authHandler,
		incomesHandler: incomesHandler,
	}
}

func (r *Router) Setup(app *fiber.App) {
	apiGroup := app.Group("/v1")

	authGroup := apiGroup.Group("/auth")

	authGroup.Post("/register", r.authHandler.RegisterMapper, r.authHandler.Register)
	authGroup.Post("/login", r.authHandler.LoginMapper, r.authHandler.Login)
	authGroup.Post("/logout", r.authHandler.Logout)
	authGroup.Post("/refresh", r.authHandler.Refresh)
	authGroup.Get("/profile", r.authHandler.GetProfile)

	incomesGroup := apiGroup.Group("/incomes")
	incomesGroup.Get("/:year/:month", r.incomesHandler.GetMapper, r.incomesHandler.Get)
	incomesGroup.Post("/", r.incomesHandler.CreateMapper, r.incomesHandler.Create)
}
