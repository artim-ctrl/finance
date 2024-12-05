package handlers

import (
	"time"

	"github.com/gofiber/fiber/v2"

	"github.com/artim-ctrl/finance/internal/auth/cookie_manager"
	"github.com/artim-ctrl/finance/internal/servers/http/response"
)

func (h *Handler) Logout(c *fiber.Ctx) error {
	h.cookieManager.SetCookie(c, cookie_manager.AccessTokenName, "", -time.Second)
	h.cookieManager.SetCookie(c, cookie_manager.RefreshTokenName, "", -time.Second)

	return response.NoContent(c)
}
