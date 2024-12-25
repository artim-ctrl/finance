package handlers

import (
	"time"

	"github.com/gofiber/fiber/v2"

	"github.com/artim-ctrl/finance/internal/servers/http/response"
)

type GetRequest struct {
	From time.Time
	To   time.Time
}

func (h *Handler) GetMapper(c *fiber.Ctx) error {
	var (
		from, to time.Time
		err      error
	)
	from, err = time.Parse(time.DateOnly, c.Query("from"))
	if err != nil {
		return response.ValidationError(c, map[string][]string{
			"from": {"Couldn't parse from date"},
		})
	}

	to, err = time.Parse(time.DateOnly, c.Query("to"))
	if err != nil {
		return response.ValidationError(c, map[string][]string{
			"to": {"Couldn't parse to date"},
		})
	}

	c.Locals("req", GetRequest{
		From: from,
		To:   to,
	})

	return c.Next()
}

func (h *Handler) Get(c *fiber.Ctx) error {
	req := c.Locals("req").(GetRequest)

	expenses, err := h.repo.Get(c.UserContext(), req.From, req.To)
	if err != nil {
		return response.Error(c, err.Error())
	}

	return response.JSON(c, expenses)
}
