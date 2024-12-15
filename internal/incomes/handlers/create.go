package handlers

import (
	"time"

	"github.com/gofiber/fiber/v2"

	"github.com/artim-ctrl/finance/internal/incomes/repositories"
	"github.com/artim-ctrl/finance/internal/models"
	"github.com/artim-ctrl/finance/internal/servers/http/response"
)

type CreateRequest struct {
	Date         time.Time `validate:"required"`
	CategoryId   *int64    `json:"categoryId,omitempty" validate:"required_without=CategoryName"`
	CategoryName *string   `json:"categoryName,omitempty" validate:"required_without=CategoryId"`
	Amount       float64   `json:"amount" validate:"required,gte=0.01"`
}

func (h *Handler) CreateMapper(c *fiber.Ctx) error {
	var (
		year, month int
		err         error
	)

	year, err = c.ParamsInt("year")
	if err != nil {
		return response.Error(c, "Couldn't parse year: "+err.Error())
	}

	month, err = c.ParamsInt("month")
	if err != nil {
		return response.Error(c, "Couldn't parse month: "+err.Error())
	}

	var req CreateRequest
	if err = c.BodyParser(&req); err != nil {
		return response.Error(c, "Couldn't parse request body: "+err.Error())
	}

	req.Date = time.Date(year, time.Month(month), 1, 0, 0, 0, 0, time.UTC)

	errs := h.validator.ValidateStruct(req)
	if errs != nil {
		return response.ValidationError(c, errs)
	}

	c.Locals("req", req)

	return c.Next()
}

func (h *Handler) Create(c *fiber.Ctx) error {
	req := c.Locals("req").(CreateRequest)

	user := c.Locals("user").(*models.User)

	var categoryId int64
	if req.CategoryId == nil {
		category := &repositories.IncomeCategory{
			UserID: user.ID,
			Name:   *req.CategoryName,
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
		Date:             req.Date,
		Amount:           req.Amount,
	}

	err := h.repo.UpsertIncome(c.UserContext(), income)
	if err != nil {
		return response.Error(c, "Couldn't create income: "+err.Error())
	}

	return response.Created(c)
}
