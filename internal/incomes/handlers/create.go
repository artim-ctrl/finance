package handlers

import (
	"time"

	"github.com/gofiber/fiber/v2"

	authrepo "github.com/artim-ctrl/finance/internal/auth/repositories"
	"github.com/artim-ctrl/finance/internal/incomes/repositories"
	"github.com/artim-ctrl/finance/internal/servers/http/response"
)

type CreateRequest struct {
	CategoryId *int64  `json:"category_id,omitempty"`
	Name       *string `json:"name,omitempty"`
	Amount     float64 `json:"amount"`
}

func (h *Handler) CreateMapper(c *fiber.Ctx) error {
	var req CreateRequest
	if err := c.BodyParser(&req); err != nil {
		return response.Error(c, "Couldn't parse request body: "+err.Error())
	}

	c.Locals("req", req)

	return c.Next()
}

func (h *Handler) Create(c *fiber.Ctx) error {
	req := c.Locals("req").(CreateRequest)

	user := c.Locals("user").(*authrepo.User)

	var categoryId int64
	if req.CategoryId == nil {
		category := &repositories.IncomeCategory{
			UserID: user.ID,
			Name:   *req.Name,
		}

		err := h.repo.CreateCategory(c.UserContext(), category)
		if err != nil {
			return response.Error(c, "Couldn't create category: "+err.Error())
		}

		categoryId = category.ID
	} else {
		categoryId = *req.CategoryId
	}

	income := &repositories.Income{
		IncomeCategoryID: categoryId,
		Date:             time.Now().UTC(),
		Amount:           req.Amount,
	}

	err := h.repo.CreateIncome(c.UserContext(), income)
	if err != nil {
		return response.Error(c, "Couldn't create income: "+err.Error())
	}

	return response.Created(c)
}
