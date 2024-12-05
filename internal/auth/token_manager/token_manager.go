package token_manager

import (
	"fmt"
	"strconv"
	"time"

	"github.com/golang-jwt/jwt/v5"

	"github.com/artim-ctrl/finance/internal/config"
)

const (
	AccessTokenTTL  = time.Minute * 30
	RefreshTokenTTL = time.Hour * 24 * 7
)

type TokenManager struct {
	accessSecretKey  string
	refreshSecretKey string
}

func NewTokenManager(config config.Config) *TokenManager {
	return &TokenManager{
		accessSecretKey:  config.Auth.AccessSecretKey,
		refreshSecretKey: config.Auth.RefreshSecretKey,
	}
}

func (tm *TokenManager) GenerateAccessToken(userID int64) (string, error) {
	return tm.generateToken(userID, AccessTokenTTL, tm.accessSecretKey)
}

func (tm *TokenManager) GenerateRefreshToken(userID int64) (string, error) {
	return tm.generateToken(userID, RefreshTokenTTL, tm.refreshSecretKey)
}

func (tm *TokenManager) generateToken(userID int64, ttl time.Duration, secretKey string) (string, error) {
	token := jwt.NewWithClaims(jwt.SigningMethodHS256, jwt.RegisteredClaims{
		Subject:   strconv.Itoa(int(userID)),
		ExpiresAt: &jwt.NumericDate{Time: time.Now().Add(ttl).UTC()},
	})

	return token.SignedString([]byte(secretKey))
}

func (tm *TokenManager) ParseAccessToken(accessToken string) (int64, error) {
	return tm.parseToken(accessToken, tm.accessSecretKey)
}

func (tm *TokenManager) ParseRefreshToken(refreshToken string) (int64, error) {
	return tm.parseToken(refreshToken, tm.refreshSecretKey)
}

func (tm *TokenManager) parseToken(token, secretKey string) (int64, error) {
	jwtToken, err := jwt.ParseWithClaims(token, &jwt.RegisteredClaims{}, func(token *jwt.Token) (interface{}, error) {
		if _, ok := token.Method.(*jwt.SigningMethodHMAC); !ok {
			return nil, fmt.Errorf("unexpected signing method: %v", token.Header["alg"])
		}

		return []byte(secretKey), nil
	})

	if err != nil || !jwtToken.Valid {
		return 0, err
	}

	var (
		claims *jwt.RegisteredClaims
		ok     bool
	)
	if claims, ok = jwtToken.Claims.(*jwt.RegisteredClaims); !ok {
		return 0, fmt.Errorf("invalid token claims")
	}

	var userID int
	if userID, err = strconv.Atoi(claims.Subject); err != nil {
		return 0, fmt.Errorf("user_id not found in token")
	}

	return int64(userID), nil
}
