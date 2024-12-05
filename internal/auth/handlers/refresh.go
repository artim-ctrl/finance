package handlers

import (
	"github.com/gofiber/fiber/v2"

	"github.com/artim-ctrl/finance/internal/servers/http/response"
)

func (h *Handler) Refresh(c *fiber.Ctx) error {
	refreshToken := c.Cookies("refresh_token")
	if refreshToken == "" {
		return response.Unauthorized(c)
	}

	userID, err := h.tokenManager.ValidateRefreshToken(refreshToken)
	if err != nil {
		h.setExpiredRefreshToken(c)

		return response.Unauthorized(c)
	}

	var newAccessToken string
	newAccessToken, err = h.tokenManager.GenerateAccessToken(userID)
	if err != nil {
		return response.Error(c, "Couldn't generate access token")
	}

	h.setAccessTokenCookie(c, newAccessToken)

	return response.NoContent(c)
}
