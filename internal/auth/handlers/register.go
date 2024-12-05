package handlers

import (
	"github.com/gofiber/fiber/v2"
	"golang.org/x/crypto/bcrypt"

	"github.com/artim-ctrl/finance/internal/models"
	"github.com/artim-ctrl/finance/internal/servers/http/response"
)

type RegisterRequest struct {
	Name     string `json:"name"`
	Email    string `json:"email"`
	Password string `json:"password"`
}

func (h *Handler) RegisterMapper(c *fiber.Ctx) error {
	var req RegisterRequest
	if err := c.BodyParser(&req); err != nil {
		return response.Error(c, "Couldn't parse request body")
	}

	c.Locals("req", req)

	return c.Next()
}

func (h *Handler) Register(c *fiber.Ctx) error {
	req := c.Locals("req").(RegisterRequest)

	hashedPassword, err := bcrypt.GenerateFromPassword([]byte(req.Password), bcrypt.DefaultCost)
	if err != nil {
		return response.Error(c, "Couldn't hash password")
	}

	user := &models.User{
		Name:     req.Name,
		Email:    req.Email,
		Password: string(hashedPassword),
	}

	err = h.repo.CreateUser(c.UserContext(), user)
	if err != nil {
		return response.Error(c, "Couldn't create a user")
	}

	var accessToken string
	if accessToken, err = h.tokenManager.GenerateAccessToken(user.ID); err != nil {
		return response.Error(c, "Couldn't generate access token")
	}

	var refreshToken string
	if refreshToken, err = h.tokenManager.GenerateRefreshToken(user.ID); err != nil {
		return response.Error(c, "Couldn't generate refresh token")
	}

	h.setAuthCookies(c, accessToken, refreshToken)

	return c.JSON(user)
}
