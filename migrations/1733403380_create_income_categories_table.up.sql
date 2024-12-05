CREATE TABLE income_categories
(
    id         BIGSERIAL PRIMARY KEY,
    user_id    BIGINT                                             NOT NULL,
    name       VARCHAR(40)                                        NOT NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL,
    deleted_at TIMESTAMP WITH TIME ZONE DEFAULT NULL,
    CONSTRAINT fk_user_id FOREIGN KEY (user_id) REFERENCES users (id)
);

CREATE UNIQUE INDEX unique_user_id_name ON income_categories (user_id, LOWER(name));
