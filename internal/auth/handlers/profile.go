package handlers

import (
	"github.com/gofiber/fiber/v2"

	"github.com/artim-ctrl/finance/internal/auth/repositories"
)

func (h *Handler) GetProfile(c *fiber.Ctx) error {
	accessToken := c.Cookies("access_token")
	if accessToken == "" {
		return c.JSON(nil)
	}

	userID, err := h.tokenManager.ValidateAccessToken(accessToken)
	if err != nil {
		return c.JSON(nil)
	}

	var user *repositories.User
	user, err = h.repo.GetActiveUserByID(c.UserContext(), userID)
	if err != nil {
		return c.JSON(nil)
	}

	return c.JSON(user)
}
