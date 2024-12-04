package handlers

import (
	"github.com/gofiber/fiber/v2"
)

func (h *Handler) Logout(c *fiber.Ctx) error {
	h.setExpiredCookies(c)

	return c.SendStatus(fiber.StatusNoContent)
}
