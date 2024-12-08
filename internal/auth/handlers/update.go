package handlers

import (
	"github.com/gofiber/fiber/v2"

	"github.com/artim-ctrl/finance/internal/models"
	"github.com/artim-ctrl/finance/internal/servers/http/response"
)

type UpdateRequest struct {
	Currency string `json:"currency" validate:"required,oneof=USD EUR RSD"`
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
	user := c.Locals("user").(*models.User)

	user.Currency.Currency = req.Currency

	err := h.repo.UpdateCurrency(c.UserContext(), &user.Currency)
	if err != nil {
		return response.Error(c, "Couldn't update currency: "+err.Error())
	}

	return response.Success(c)
}
