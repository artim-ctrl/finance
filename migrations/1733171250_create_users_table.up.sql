CREATE TABLE users
(
    id                 BIGSERIAL PRIMARY KEY,
    name               VARCHAR(255)        NOT NULL,
    email              VARCHAR(255) UNIQUE NOT NULL,
    password           VARCHAR(255)        NOT NULL,
    created_at         TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL,
    email_confirmed_at TIMESTAMP WITH TIME ZONE DEFAULT NULL,
    updated_at         TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL,
    deleted_at         TIMESTAMP WITH TIME ZONE DEFAULT NULL
);
