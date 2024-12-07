package handlers

import (
	"github.com/gofiber/fiber/v2"

	"github.com/artim-ctrl/finance/internal/expenses/repositories"
	"github.com/artim-ctrl/finance/internal/servers/http/response"
)

type UpdatePlanRequest struct {
	Year       int     `json:"year" validate:"required"`
	Month      int     `json:"month" validate:"required"`
	CategoryId int64   `json:"categoryId,omitempty" validate:"required"`
	Amount     float64 `json:"amount" validate:"required,gte=0.01"`
}

func (h *Handler) UpdatePlanMapper(c *fiber.Ctx) error {
	var req UpdatePlanRequest
	if err := c.BodyParser(&req); err != nil {
		return response.Error(c, "Couldn't parse request body: "+err.Error())
	}

	errs := h.validator.ValidateStruct(req)
	if errs != nil {
		return response.ValidationError(c, errs)
	}

	c.Locals("req", req)

	return c.Next()
}

func (h *Handler) UpdatePlan(c *fiber.Ctx) error {
	req := c.Locals("req").(UpdatePlanRequest)

	expensePlan := &repositories.MonthlyExpensePlan{
		ExpenseCategoryID: req.CategoryId,
		Year:              req.Year,
		Month:             req.Month,
		Amount:            req.Amount,
	}

	err := h.repo.UpsertPlan(c.UserContext(), expensePlan)
	if err != nil {
		return response.Error(c, "Couldn't create expense: "+err.Error())
	}

	return response.Success(c)
}
