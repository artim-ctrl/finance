package http

import (
	"github.com/gofiber/fiber/v2"

	"github.com/artim-ctrl/finance/internal/auth/handlers"
)

type Router struct {
	authHandler *handlers.Handler
}

func NewRouter(
	authHandler *handlers.Handler,
) *Router {
	return &Router{
		authHandler: authHandler,
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

	//securedGroup := apiGroup.Group("/", r.authHandler.AuthMiddleware())
	//securedGroup.Get("/profile", r.authHandler.GetProfile)
}
