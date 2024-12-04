package handlers

import (
	"github.com/gofiber/fiber/v2"
	"golang.org/x/crypto/bcrypt"
)

type LoginRequest struct {
	Email    string `json:"email"`
	Password string `json:"password"`
}

func (h *Handler) LoginMapper(c *fiber.Ctx) error {
	var req LoginRequest
	if err := c.BodyParser(&req); err != nil {
		return c.Status(fiber.StatusInternalServerError).JSON(fiber.Map{
			"error": "Couldn't parse request body: " + err.Error(),
		})
	}

	c.Locals("req", req)

	return c.Next()
}

func (h *Handler) Login(c *fiber.Ctx) error {
	req := c.Locals("req").(LoginRequest)

	user, err := h.repo.GetUserByEmail(c.UserContext(), req.Email)
	if err != nil {
		return c.Status(fiber.StatusUnprocessableEntity).JSON(fiber.Map{
			"email": []string{"Email or password is incorrect"},
		})
	}

	if err = bcrypt.CompareHashAndPassword([]byte(user.Password), []byte(req.Password)); err != nil {
		return c.Status(fiber.StatusUnprocessableEntity).JSON(fiber.Map{
			"email": []string{"Email or password is incorrect"},
		})
	}

	var accessToken, refreshToken string
	if accessToken, refreshToken, err = h.tokenManager.GenerateTokens(user.ID); err != nil {
		return c.Status(fiber.StatusInternalServerError).JSON(fiber.Map{
			"error": "Couldn't generate access token",
		})
	}

	h.setAuthCookies(c, accessToken, refreshToken)

	return c.JSON(user)
}
