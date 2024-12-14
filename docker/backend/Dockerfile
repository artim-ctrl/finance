FROM golang:1.23-alpine as build

WORKDIR /app
COPY . .
RUN go build -o main cmd/finance/main.go

FROM alpine:latest

WORKDIR /app
COPY --from=build /app/main .
COPY ./config /app/config
COPY ./migrations /app/migrations

CMD ["/app/main", "-env", "prod"]
