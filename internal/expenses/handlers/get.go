package handlers

import (
	"time"

	"github.com/gofiber/fiber/v2"

	"github.com/artim-ctrl/finance/internal/models"
	"github.com/artim-ctrl/finance/internal/servers/http/response"
)

type GetRequest struct {
	Date time.Time `params:"date"`
}

func (h *Handler) GetMapper(c *fiber.Ctx) error {
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

	req := GetRequest{
		Date: time.Date(year, time.Month(month), 1, 0, 0, 0, 0, time.UTC),
	}

	c.Locals("req", req)

	return c.Next()
}

func (h *Handler) Get(c *fiber.Ctx) error {
	req := c.Locals("req").(GetRequest)

	user := c.Locals("user").(*models.User)

	expenses, err := h.repo.GetByDate(c.UserContext(), user.ID, req.Date)
	if err != nil {
		return response.Error(c, "Couldn't get expense categories: "+err.Error())
	}

	return response.JSON(c, expenses)
}
