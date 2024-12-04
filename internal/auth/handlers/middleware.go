package handlers

import (
	"database/sql"
	"errors"

	"github.com/gofiber/fiber/v2"

	"github.com/artim-ctrl/finance/internal/auth/repositories"
)

func (h *Handler) AuthMiddleware() fiber.Handler {
	return func(c *fiber.Ctx) error {
		accessToken := c.Cookies("access_token")
		if accessToken == "" {
			return fiber.NewError(fiber.StatusUnauthorized, "Access token not provided")
		}

		userID, err := h.tokenManager.ValidateAccessToken(accessToken)
		if err != nil {
			return fiber.NewError(fiber.StatusUnauthorized, "Invalid or expired access token")
		}

		var user *repositories.User
		user, err = h.repo.GetActiveUserByID(c.UserContext(), userID)
		if errors.Is(err, sql.ErrNoRows) {
			return fiber.NewError(fiber.StatusUnauthorized, "Invalid or expired access token")
		} else if err != nil {
			return fiber.NewError(fiber.StatusInternalServerError, "Error retrieving user")
		}

		c.Locals("user", user)

		return c.Next()
	}
}
