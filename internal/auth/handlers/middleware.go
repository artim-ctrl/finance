package handlers

import (
	"database/sql"
	"errors"
	"strings"

	"github.com/gofiber/fiber/v2"
	"github.com/gofiber/fiber/v2/middleware/keyauth"

	"github.com/artim-ctrl/finance/internal/auth/cookie_manager"
	"github.com/artim-ctrl/finance/internal/models"
)

func (h *Handler) validateAPIKey(c *fiber.Ctx, accessToken string) (bool, error) {
	userID, err := h.tokenManager.ParseAccessToken(accessToken)
	if err != nil {
		return false, err
	}

	var user *models.User
	user, err = h.repo.GetActiveUserByID(c.UserContext(), userID)
	if errors.Is(err, sql.ErrNoRows) {
		return false, nil
	}

	c.Locals("user", user)

	return true, nil
}

func (h *Handler) filterIsPublic(c *fiber.Ctx) bool {
	originalUrl := strings.ToLower(c.OriginalURL())

	return strings.HasPrefix(originalUrl, "/v1/auth") && originalUrl != "/v1/auth/profile"
}

func (h *Handler) AuthMiddleware() fiber.Handler {
	return keyauth.New(keyauth.Config{
		Next:      h.filterIsPublic,
		KeyLookup: "cookie:" + cookie_manager.AccessTokenName,
		Validator: h.validateAPIKey,
		ErrorHandler: func(c *fiber.Ctx, err error) error {
			if errors.Is(err, keyauth.ErrMissingOrMalformedAPIKey) {
				return fiber.NewError(fiber.StatusUnauthorized, "Access token not provided")
			}

			return fiber.NewError(fiber.StatusUnauthorized, "Invalid or expired access token")
		},
	})
}
