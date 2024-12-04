package handlers

import (
	"time"

	"github.com/gofiber/fiber/v2"

	"github.com/artim-ctrl/finance/internal/auth/repositories"
	"github.com/artim-ctrl/finance/internal/auth/tokens"
	"github.com/artim-ctrl/finance/internal/environment"
)

type Handler struct {
	repo         *repositories.Repository
	tokenManager *tokens.TokenManager
	env          environment.Variables
}

func NewHandler(
	repo *repositories.Repository,
	tokenManager *tokens.TokenManager,
	env environment.Variables,
) *Handler {
	return &Handler{
		repo:         repo,
		tokenManager: tokenManager,
		env:          env,
	}
}

func (h *Handler) setAuthCookies(c *fiber.Ctx, accessToken, refreshToken string) {
	accessTokenCookie := &fiber.Cookie{
		Name:     "access_token",
		Value:    accessToken,
		Expires:  time.Now().Add(tokens.AccessTokenTTL),
		HTTPOnly: true,
		Secure:   false,
	}
	refreshTokenCookie := &fiber.Cookie{
		Name:     "refresh_token",
		Value:    refreshToken,
		Expires:  time.Now().Add(tokens.RefreshTokenTTL),
		HTTPOnly: true,
		Secure:   false,
	}

	if h.env.IsProd() {
		accessTokenCookie.SameSite = "Strict"
		accessTokenCookie.Secure = true
		refreshTokenCookie.SameSite = "Strict"
		refreshTokenCookie.Secure = true
	}

	c.Cookie(accessTokenCookie)
	c.Cookie(refreshTokenCookie)
}

func (h *Handler) setExpiredCookies(c *fiber.Ctx) {
	accessTokenCookie := &fiber.Cookie{
		Name:     "access_token",
		Value:    "",
		Expires:  time.Now().Add(-time.Hour),
		HTTPOnly: true,
		Secure:   false,
	}
	refreshTokenCookie := &fiber.Cookie{
		Name:     "refresh_token",
		Value:    "",
		Expires:  time.Now().Add(-time.Hour),
		HTTPOnly: true,
		Secure:   false,
	}

	if h.env.IsProd() {
		accessTokenCookie.SameSite = "Strict"
		accessTokenCookie.Secure = true
		refreshTokenCookie.SameSite = "Strict"
		refreshTokenCookie.Secure = true
	}

	c.Cookie(accessTokenCookie)
	c.Cookie(refreshTokenCookie)
}
