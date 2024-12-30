package handlers

import (
	"time"

	"github.com/gofiber/fiber/v2"

	"github.com/artim-ctrl/finance/internal/charts/repositories"
	"github.com/artim-ctrl/finance/internal/models"
	"github.com/artim-ctrl/finance/internal/servers/http/response"
)

type GetRequest struct {
	Categories []int64 `query:"categories"`
}

func (h *Handler) GetMapper(c *fiber.Ctx) error {
	var req GetRequest
	if err := c.QueryParser(&req); err != nil {
		return response.Error(c, "Couldn't parse request")
	}

	c.Locals("req", req)

	return c.Next()
}

type ExpensesResponse struct {
	Expenses   []repositories.ExpenseData     `json:"expenses"`
	Categories []repositories.ExpenseCategory `json:"categories"`
}

func (h *Handler) Get(c *fiber.Ctx) error {
	req := c.Locals("req").(GetRequest)
	user := c.Locals("user").(*models.User)

	from := time.Date(2024, time.December, 1, 0, 0, 0, 0, time.UTC)
	to := time.Date(2024, time.December, 31, 0, 0, 0, 0, time.UTC)

	fromPrev := time.Date(2024, time.November, 1, 0, 0, 0, 0, time.UTC)
	toPrev := time.Date(2024, time.November, 30, 0, 0, 0, 0, time.UTC)

	expenses, err := h.repo.Get(c.UserContext(), req.Categories, user.ID, from, to, fromPrev, toPrev)
	if err != nil {
		return response.Error(c, err.Error())
	}

	var categories []repositories.ExpenseCategory
	categories, err = h.repo.GetCategories(c.UserContext(), user.ID, from, to, fromPrev, toPrev)
	if err != nil {
		return response.Error(c, err.Error())
	}

	return response.JSON(c, ExpensesResponse{
		Expenses:   expenses,
		Categories: categories,
	})
}
