CREATE TABLE expenses
(
    id                  BIGSERIAL PRIMARY KEY,
    expense_category_id BIGINT                                             NOT NULL,
    date                DATE                                               NOT NULL,
    amount              NUMERIC(12, 2)                                     NOT NULL,
    comment             TEXT                     DEFAULT NULL,
    created_at          TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL,
    updated_at          TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL,
    deleted_at          TIMESTAMP WITH TIME ZONE DEFAULT NULL,
    CONSTRAINT fk_expense_category FOREIGN KEY (expense_category_id) REFERENCES expense_categories (id)
);
