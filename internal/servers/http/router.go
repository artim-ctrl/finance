package http

import (
	"github.com/gofiber/fiber/v2"

	ahandler "github.com/artim-ctrl/finance/internal/auth/handlers"
	ehandler "github.com/artim-ctrl/finance/internal/expenses/handlers"
	ihandler "github.com/artim-ctrl/finance/internal/incomes/handlers"
)

type Router struct {
	authHandler     *ahandler.Handler
	incomesHandler  *ihandler.Handler
	expensesHandler *ehandler.Handler
}

func NewRouter(
	authHandler *ahandler.Handler,
	incomesHandler *ihandler.Handler,
	expensesHandler *ehandler.Handler,
) *Router {
	return &Router{
		authHandler:     authHandler,
		incomesHandler:  incomesHandler,
		expensesHandler: expensesHandler,
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
	authGroup.Put("/profile", r.authHandler.UpdateMapper, r.authHandler.Update)

	incomesGroup := apiGroup.Group("/incomes")
	incomesGroup.Get("/:year/:month", r.incomesHandler.GetMapper, r.incomesHandler.Get)
	incomesGroup.Post("/", r.incomesHandler.CreateMapper, r.incomesHandler.Create)
	incomesGroup.Put("/", r.incomesHandler.UpdateMapper, r.incomesHandler.Update)

	expensesGroup := apiGroup.Group("/expenses")
	expensesGroup.Get("/:year/:month", r.expensesHandler.GetMapper, r.expensesHandler.Get)
	expensesGroup.Post("/", r.expensesHandler.CreateMapper, r.expensesHandler.Create)

	expensePlansGroup := expensesGroup.Group("/plans")
	expensePlansGroup.Put("/", r.expensesHandler.UpdatePlanMapper, r.expensesHandler.UpdatePlan)
}
