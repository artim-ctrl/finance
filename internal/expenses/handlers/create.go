package handlers

import (
	"time"

	"github.com/gofiber/fiber/v2"

	"github.com/artim-ctrl/finance/internal/expenses/repositories"
	"github.com/artim-ctrl/finance/internal/models"
	"github.com/artim-ctrl/finance/internal/servers/http/response"
)

type Date struct {
	time.Time
}

func (d *Date) UnmarshalJSON(b []byte) error {
	date, err := time.Parse(time.DateOnly, string(b))
	if err != nil {
		return err
	}

	d.Time = date

	return err
}

type CreateRequest struct {
	Date         Date    `json:"date"`
	CategoryId   *int64  `json:"categoryId,omitempty" validate:"required_without=CategoryName"`
	CategoryName *string `json:"categoryName,omitempty" validate:"required_without=CategoryId"`
	Amount       float64 `json:"amount" validate:"required,gte=0.01"`
}

func (h *Handler) CreateMapper(c *fiber.Ctx) error {
	var req CreateRequest
	if err := c.BodyParser(&req); err != nil {
		return response.Error(c, "Couldn't parse request body: "+err.Error())
	}

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
		category := &repositories.ExpenseCategory{
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

	expense := &repositories.Expense{
		ExpenseCategoryID: categoryId,
		Date:              req.Date.Time,
		Amount:            req.Amount,
	}

	err := h.repo.CreateExpense(c.UserContext(), expense)
	if err != nil {
		return response.Error(c, "Couldn't create expense: "+err.Error())
	}

	return response.Created(c)
}
