package handlers

import (
	"github.com/gofiber/fiber/v2"
	"golang.org/x/crypto/bcrypt"

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

	var accessToken, refreshToken string
	if accessToken, refreshToken, err = h.tokenManager.GenerateTokens(user.ID); err != nil {
		return response.Error(c, "Couldn't generate access token")
	}

	h.setAuthCookies(c, accessToken, refreshToken)

	return response.JSON(c, user)
}
