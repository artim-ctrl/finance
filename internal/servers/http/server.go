package http

import (
	"context"
	"errors"
	"fmt"
	"net/http"
	"runtime/debug"
	"time"

	"github.com/gofiber/contrib/fiberzap/v2"
	"github.com/gofiber/fiber/v2"
	"github.com/gofiber/fiber/v2/middleware/cors"
	"github.com/gofiber/fiber/v2/middleware/recover"
	"go.uber.org/zap"

	"github.com/artim-ctrl/finance/internal/auth/handlers"
	"github.com/artim-ctrl/finance/internal/config"
)

type Server struct {
	server *fiber.App
	logger *zap.Logger
}

func New(router *Router, logger *zap.Logger, config config.Config, authHandler *handlers.Handler) *Server {
	serverName := "http"

	logger = logger.With(zap.String("server", serverName))

	server := fiber.New(fiber.Config{
		AppName:                  serverName,
		DisableStartupMessage:    true,
		ReadTimeout:              15 * time.Second,
		EnableSplittingOnParsers: true,
		Immutable:                true,
	})

	server.Use(recover.New(recover.Config{
		StackTraceHandler: func(c *fiber.Ctx, e any) {
			logger.Error(fmt.Sprintf("panic: %v\n%s\n", e, debug.Stack()), zap.ByteString("url", c.Request().RequestURI()))
		},
	}))
	server.Use(fiberzap.New(fiberzap.Config{
		Logger:   logger,
		Messages: []string{"server error", "client error", "success"},
		Fields:   []string{"status", "method", "url", "body", "ua", "queryParams", "error"},
	}))
	server.Use(cors.New(cors.Config{
		AllowOrigins:     config.Frontend.BaseUrl,
		AllowHeaders:     "Origin, X-Requested-With, Content-Type, Accept",
		AllowCredentials: true,
	}))
	server.Use(authHandler.AuthMiddleware())

	router.Setup(server)

	s := &Server{
		logger: logger,
	}

	server.Use(func(c *fiber.Ctx) error {
		return c.SendStatus(fiber.StatusNotFound)
	})

	s.server = server

	return s
}

func (s *Server) Start(_ context.Context) {
	go func() {
		address := ":8080"

		s.logger.Info("starting server", zap.String("address", address))

		if err := s.server.Listen(address); err != nil && !errors.Is(err, http.ErrServerClosed) {
			s.logger.Fatal("error while starting server", zap.Error(err))
		}
	}()
}

func (s *Server) Stop(_ context.Context) {
	s.logger.Info("stopping server")

	if err := s.server.Shutdown(); err != nil && !errors.Is(err, http.ErrServerClosed) {
		s.logger.Error("error while stopping server", zap.Error(err))
	}
}
