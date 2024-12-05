package handlers

import (
	"github.com/gofiber/fiber/v2"

	"github.com/artim-ctrl/finance/internal/servers/http/response"
)

func (h *Handler) Logout(c *fiber.Ctx) error {
	h.setExpiredCookies(c)

	return response.NoContent(c)
}
