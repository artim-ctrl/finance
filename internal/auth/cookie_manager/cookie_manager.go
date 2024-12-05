package cookie_manager

import (
	"time"

	"github.com/gofiber/fiber/v2"

	"github.com/artim-ctrl/finance/internal/environment"
)

const (
	AccessTokenName  = "access_token"
	RefreshTokenName = "refresh_token"
)

type CookieManager struct {
	env environment.Variables
}

func NewCookieManager(env environment.Variables) *CookieManager {
	return &CookieManager{
		env: env,
	}
}

func (cm *CookieManager) SetCookie(c *fiber.Ctx, name, value string, ttl time.Duration) {
	accessTokenCookie := &fiber.Cookie{
		Name:     name,
		Value:    value,
		Expires:  time.Now().Add(ttl),
		HTTPOnly: true,
		Secure:   false,
	}

	if cm.env.IsProd() {
		accessTokenCookie.SameSite = "Strict"
		accessTokenCookie.Secure = true
	}

	c.Cookie(accessTokenCookie)
}
