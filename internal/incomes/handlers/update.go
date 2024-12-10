package handlers

import (
	"github.com/gofiber/fiber/v2"

	"github.com/artim-ctrl/finance/internal/incomes/repositories"
	"github.com/artim-ctrl/finance/internal/servers/http/response"
)

type UpdateRequest struct {
	Amount float64 `json:"amount" validate:"required,gte=0.01"`
	ID     int64   `json:"id"`
}

func (h *Handler) UpdateMapper(c *fiber.Ctx) error {
	var req UpdateRequest
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

func (h *Handler) Update(c *fiber.Ctx) error {
	req := c.Locals("req").(UpdateRequest)

	income := &repositories.Income{
		ID:     req.ID,
		Amount: req.Amount,
	}

	err := h.repo.UpdateIncome(c.UserContext(), income)
	if err != nil {
		return response.Error(c, "Couldn't create income: "+err.Error())
	}

	return response.Created(c)
}
