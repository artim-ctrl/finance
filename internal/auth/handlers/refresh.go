package handlers

import (
	"github.com/gofiber/fiber/v2"
)

func (h *Handler) Refresh(c *fiber.Ctx) error {
	refreshToken := c.Cookies("refresh_token")
	if refreshToken == "" {
		return c.SendStatus(fiber.StatusUnauthorized)
	}

	userID, err := h.tokenManager.ValidateRefreshToken(refreshToken)
	if err != nil {
		h.setExpiredRefreshToken(c)

		return c.SendStatus(fiber.StatusUnauthorized)
	}

	var newAccessToken string
	newAccessToken, err = h.tokenManager.GenerateAccessToken(userID)
	if err != nil {
		return c.Status(fiber.StatusInternalServerError).JSON(fiber.Map{
			"error": "Couldn't generate access token",
		})
	}

	h.setAccessTokenCookie(c, newAccessToken)

	return c.SendStatus(fiber.StatusNoContent)
}
