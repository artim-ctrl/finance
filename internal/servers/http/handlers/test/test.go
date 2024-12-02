package test

import (
	"encoding/json"

	"github.com/gofiber/fiber/v2"
	"github.com/uptrace/bun"

	"github.com/artim-ctrl/finance/internal/database/postgres"
)

type Handler struct {
	conn *postgres.Conn
}

func New(conn *postgres.Conn) *Handler {
	return &Handler{
		conn: conn,
	}
}

type Migration struct {
	bun.BaseModel `bun:"table:migrations,alias:m"`

	Id   int64  `bun:"id,pk,autoincrement" json:"id"`
	Name string `bun:"name" json:"name"`
}

func (t *Handler) Handle(ctx *fiber.Ctx) error {
	var (
		migrations = make([]Migration, 0)
		err        error
	)

	q := t.conn.NewSelect().Model(&migrations)
	if err = q.Scan(ctx.UserContext()); err != nil {
		return err
	}

	var r []byte
	if r, err = json.Marshal(migrations); err != nil {
		return err
	}

	return ctx.Send(r)
}
