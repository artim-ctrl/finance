CREATE TABLE expense_categories
(
    id         BIGSERIAL PRIMARY KEY,
    user_id    BIGINT                                             NOT NULL,
    name       VARCHAR(40)                                        NOT NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL,
    deleted_at TIMESTAMP WITH TIME ZONE DEFAULT NULL,
    CONSTRAINT fk_user_id FOREIGN KEY (user_id) REFERENCES users (id)
);

CREATE UNIQUE INDEX unique_expense_categories_user_id_name ON expense_categories (user_id, LOWER(name));
