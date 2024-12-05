package response

import "github.com/gofiber/fiber/v2"

func Error(c *fiber.Ctx, err string) error {
	return c.Status(fiber.StatusInternalServerError).JSON(fiber.Map{
		"error": err,
	})
}

func Created(c *fiber.Ctx) error {
	return c.Status(fiber.StatusCreated).JSON(fiber.Map{})
}

func NoContent(c *fiber.Ctx) error {
	return c.Status(fiber.StatusNoContent).JSON(fiber.Map{})
}

func Success(c *fiber.Ctx) error {
	return c.Status(fiber.StatusOK).JSON(fiber.Map{})
}

func JSON(c *fiber.Ctx, data any) error {
	return c.Status(fiber.StatusOK).JSON(data)
}

func Unauthorized(c *fiber.Ctx) error {
	return c.Status(fiber.StatusUnauthorized).JSON(fiber.Map{})
}

func ValidationError(c *fiber.Ctx, errors map[string][]string) error {
	return c.Status(fiber.StatusUnprocessableEntity).JSON(errors)
}
