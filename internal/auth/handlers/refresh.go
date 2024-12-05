package handlers

import (
	"time"

	"github.com/gofiber/fiber/v2"

	"github.com/artim-ctrl/finance/internal/auth/cookie_manager"
	"github.com/artim-ctrl/finance/internal/auth/token_manager"
	"github.com/artim-ctrl/finance/internal/servers/http/response"
)

func (h *Handler) Refresh(c *fiber.Ctx) error {
	refreshToken := c.Cookies(cookie_manager.RefreshTokenName)
	if refreshToken == "" {
		return response.Unauthorized(c)
	}

	userID, err := h.tokenManager.ParseRefreshToken(refreshToken)
	if err != nil {
		h.cookieManager.SetCookie(c, cookie_manager.RefreshTokenName, "", -time.Second)

		return response.Unauthorized(c)
	}

	var newAccessToken string
	newAccessToken, err = h.tokenManager.GenerateAccessToken(userID)
	if err != nil {
		return response.Error(c, "Couldn't generate access token")
	}

	h.cookieManager.SetCookie(c, cookie_manager.AccessTokenName, newAccessToken, token_manager.AccessTokenTTL)

	return response.NoContent(c)
}
