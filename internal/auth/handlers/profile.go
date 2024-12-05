package handlers

import (
	"github.com/gofiber/fiber/v2"

	"github.com/artim-ctrl/finance/internal/auth/repositories"
	"github.com/artim-ctrl/finance/internal/servers/http/response"
)

func (h *Handler) GetProfile(c *fiber.Ctx) error {
	accessToken := c.Cookies("access_token")
	if accessToken == "" {
		return response.JSON(c, nil)
	}

	userID, err := h.tokenManager.ValidateAccessToken(accessToken)
	if err != nil {
		return response.JSON(c, nil)
	}

	var user *repositories.User
	user, err = h.repo.GetActiveUserByID(c.UserContext(), userID)
	if err != nil {
		return response.JSON(c, nil)
	}

	return response.JSON(c, user)
}
