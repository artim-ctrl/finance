package tokens

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

func (tm *TokenManager) GenerateTokens(userID int64) (string, string, error) {
	var (
		accessToken, refreshToken string
		err                       error
	)

	accessToken, err = tm.generateToken(userID, AccessTokenTTL, tm.accessSecretKey)
	if err != nil {
		return "", "", err
	}

	refreshToken, err = tm.generateToken(userID, RefreshTokenTTL, tm.refreshSecretKey)
	if err != nil {
		return "", "", err
	}

	return accessToken, refreshToken, nil
}

func (tm *TokenManager) GenerateAccessToken(userID int64) (string, error) {
	return tm.generateToken(userID, AccessTokenTTL, tm.accessSecretKey)
}

func (tm *TokenManager) generateToken(userID int64, ttl time.Duration, secretKey string) (string, error) {
	token := jwt.NewWithClaims(jwt.SigningMethodHS256, jwt.RegisteredClaims{
		Subject:   strconv.Itoa(int(userID)),
		ExpiresAt: &jwt.NumericDate{Time: time.Now().Add(ttl).UTC()},
	})

	return token.SignedString([]byte(secretKey))
}

func (tm *TokenManager) ValidateAccessToken(token string) (int64, error) {
	return tm.validateToken(token, tm.accessSecretKey)
}

func (tm *TokenManager) ValidateRefreshToken(token string) (int64, error) {
	return tm.validateToken(token, tm.refreshSecretKey)
}

func (tm *TokenManager) validateToken(tokenStr, secretKey string) (int64, error) {
	token, err := jwt.ParseWithClaims(tokenStr, &jwt.RegisteredClaims{}, func(token *jwt.Token) (interface{}, error) {
		if _, ok := token.Method.(*jwt.SigningMethodHMAC); !ok {
			return nil, fmt.Errorf("unexpected signing method: %v", token.Header["alg"])
		}

		return []byte(secretKey), nil
	})

	if err != nil || !token.Valid {
		return 0, err
	}

	var (
		claims *jwt.RegisteredClaims
		ok     bool
	)
	if claims, ok = token.Claims.(*jwt.RegisteredClaims); !ok {
		return 0, fmt.Errorf("invalid token claims")
	}

	var userID int
	if userID, err = strconv.Atoi(claims.Subject); err != nil {
		return 0, fmt.Errorf("user_id not found in token")
	}

	return int64(userID), nil
}
