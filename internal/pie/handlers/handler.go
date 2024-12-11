package handlers

import (
	"github.com/gofiber/fiber/v2"

	"github.com/artim-ctrl/finance/internal/pie/repositories"
	"github.com/artim-ctrl/finance/internal/servers/http/response"
)

type Handler struct {
	repo *repositories.Repository
}

func NewHandler(repo *repositories.Repository) *Handler {
	return &Handler{repo: repo}
}

func (h *Handler) Get(c *fiber.Ctx) error {
	expenses, err := h.repo.Get(c.UserContext())
	if err != nil {
		return response.Error(c, err.Error())
	}

	return response.JSON(c, expenses)
}
