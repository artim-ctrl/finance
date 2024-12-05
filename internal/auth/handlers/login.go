package handlers

import (
	"github.com/gofiber/fiber/v2"
	"golang.org/x/crypto/bcrypt"

	"github.com/artim-ctrl/finance/internal/auth/cookie_manager"
	"github.com/artim-ctrl/finance/internal/auth/token_manager"
	"github.com/artim-ctrl/finance/internal/servers/http/response"
)

type LoginRequest struct {
	Email    string `json:"email"`
	Password string `json:"password"`
}

func (h *Handler) LoginMapper(c *fiber.Ctx) error {
	var req LoginRequest
	if err := c.BodyParser(&req); err != nil {
		return response.Error(c, "Couldn't parse request body: "+err.Error())
	}

	c.Locals("req", req)

	return c.Next()
}

func (h *Handler) Login(c *fiber.Ctx) error {
	req := c.Locals("req").(LoginRequest)

	user, err := h.repo.GetUserByEmail(c.UserContext(), req.Email)
	if err != nil {
		return response.ValidationError(c, map[string][]string{
			"email": {"Email or password is incorrect"},
		})
	}

	if err = bcrypt.CompareHashAndPassword([]byte(user.Password), []byte(req.Password)); err != nil {
		return response.ValidationError(c, map[string][]string{
			"email": {"Email or password is incorrect"},
		})
	}

	var accessToken string
	if accessToken, err = h.tokenManager.GenerateAccessToken(user.ID); err != nil {
		return response.Error(c, "Couldn't generate access token")
	}

	var refreshToken string
	if refreshToken, err = h.tokenManager.GenerateRefreshToken(user.ID); err != nil {
		return response.Error(c, "Couldn't generate refresh token")
	}

	h.cookieManager.SetCookie(c, cookie_manager.AccessTokenName, accessToken, token_manager.AccessTokenTTL)
	h.cookieManager.SetCookie(c, cookie_manager.RefreshTokenName, refreshToken, token_manager.RefreshTokenTTL)

	return response.JSON(c, user)
}
