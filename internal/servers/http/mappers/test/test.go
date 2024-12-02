package test

import "github.com/gofiber/fiber/v2"

type Mapper struct{}

func New() *Mapper {
	return &Mapper{}
}

type Request struct {
	t string `query:"t"`
}

func (m *Mapper) Map(ctx *fiber.Ctx) error {
	var req Request
	if err := ctx.QueryParser(&req); err != nil {
		return err
	}

	ctx.Locals("req", req)

	return ctx.Next()
}
