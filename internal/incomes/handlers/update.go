package handlers

import (
	"time"

	"github.com/gofiber/fiber/v2"

	"github.com/artim-ctrl/finance/internal/incomes/repositories"
	"github.com/artim-ctrl/finance/internal/servers/http/response"
)

type UpdateRequest struct {
	Date       time.Time `validate:"required"`
	Amount     float64   `json:"amount" validate:"required,gte=0.01"`
	CategoryID int64     `json:"category_id"`
}

func (h *Handler) UpdateMapper(c *fiber.Ctx) error {
	var (
		year, month int
		err         error
	)

	year, err = c.ParamsInt("year")
	if err != nil {
		return response.Error(c, "Couldn't parse year: "+err.Error())
	}

	month, err = c.ParamsInt("month")
	if err != nil {
		return response.Error(c, "Couldn't parse month: "+err.Error())
	}

	var req UpdateRequest
	if err := c.BodyParser(&req); err != nil {
		return response.Error(c, "Couldn't parse request body: "+err.Error())
	}

	req.Date = time.Date(year, time.Month(month), 1, 0, 0, 0, 0, time.UTC)

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
		IncomeCategoryID: req.CategoryID,
		Date:             req.Date,
		Amount:           req.Amount,
	}

	err := h.repo.UpdateIncome(c.UserContext(), income)
	if err != nil {
		return response.Error(c, "Couldn't create income: "+err.Error())
	}

	return response.Created(c)
}
