package http

import (
	"github.com/gofiber/fiber/v2"

	ahandler "github.com/artim-ctrl/finance/internal/auth/handlers"
	chandler "github.com/artim-ctrl/finance/internal/charts/handlers"
	ehandler "github.com/artim-ctrl/finance/internal/expenses/handlers"
	ihandler "github.com/artim-ctrl/finance/internal/incomes/handlers"
	phandler "github.com/artim-ctrl/finance/internal/pie/handlers"
)

type Router struct {
	authHandler     *ahandler.Handler
	incomesHandler  *ihandler.Handler
	expensesHandler *ehandler.Handler
	chartsHandler   *chandler.Handler
	pieHandler      *phandler.Handler
}

func NewRouter(
	authHandler *ahandler.Handler,
	incomesHandler *ihandler.Handler,
	expensesHandler *ehandler.Handler,
	chartsHandler *chandler.Handler,
	pieHandler *phandler.Handler,
) *Router {
	return &Router{
		authHandler:     authHandler,
		incomesHandler:  incomesHandler,
		expensesHandler: expensesHandler,
		chartsHandler:   chartsHandler,
		pieHandler:      pieHandler,
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

	chartsGroup := apiGroup.Group("/charts")
	chartsGroup.Get("/", r.chartsHandler.Get)

	pieGroup := apiGroup.Group("/pie")
	pieGroup.Get("/", r.pieHandler.Get)
}
