package handlers

import (
	"github.com/gofiber/fiber/v2"
	"golang.org/x/crypto/bcrypt"

	"github.com/artim-ctrl/finance/internal/auth/cookie_manager"
	"github.com/artim-ctrl/finance/internal/auth/token_manager"
	"github.com/artim-ctrl/finance/internal/models"
	"github.com/artim-ctrl/finance/internal/servers/http/response"
)

type RegisterRequest struct {
	Name     string `json:"name" validate:"required,min=6,max=255"`
	Email    string `json:"email" validate:"required,email,max=255"`
	Password string `json:"password" validate:"required,min=8,max=32"`
	Currency string `json:"currency" validate:"required,oneof=USD EUR RSD"`
}

func (h *Handler) RegisterMapper(c *fiber.Ctx) error {
	var req RegisterRequest
	if err := c.BodyParser(&req); err != nil {
		return response.Error(c, "Couldn't parse request body")
	}

	errs := h.validator.ValidateStruct(req)
	if errs != nil {
		return response.ValidationError(c, errs)
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

	currency := &models.UserCurrency{
		UserID:   user.ID,
		Currency: req.Currency,
	}

	err = h.repo.CreateCurrency(c.UserContext(), currency)
	if err != nil {
		return response.Error(c, "Couldn't create a currency")
	}

	user.Currency = *currency

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

	return c.JSON(user)
}
