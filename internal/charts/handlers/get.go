package handlers

import (
	"time"

	"github.com/gofiber/fiber/v2"

	"github.com/artim-ctrl/finance/internal/models"
	"github.com/artim-ctrl/finance/internal/servers/http/response"
)

func (h *Handler) Get(c *fiber.Ctx) error {
	user := c.Locals("user").(*models.User)

	from := time.Date(2024, time.December, 1, 0, 0, 0, 0, time.UTC)
	to := time.Date(2024, time.December, 31, 0, 0, 0, 0, time.UTC)

	fromPrev := time.Date(2024, time.November, 1, 0, 0, 0, 0, time.UTC)
	toPrev := time.Date(2024, time.November, 30, 0, 0, 0, 0, time.UTC)

	expenses, err := h.repo.Get(c.UserContext(), user.ID, from, to, fromPrev, toPrev)
	if err != nil {
		return response.Error(c, err.Error())
	}

	return response.JSON(c, expenses)
}
