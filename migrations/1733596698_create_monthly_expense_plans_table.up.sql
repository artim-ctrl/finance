CREATE TABLE monthly_expense_plans
(
    id                  BIGSERIAL PRIMARY KEY,
    expense_category_id BIGINT                                             NOT NULL,
    year                INT                                                NOT NULL,
    month               INT                                                NOT NULL,
    amount              NUMERIC(12, 2)                                     NOT NULL,
    created_at          TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL,
    updated_at          TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL,
    CONSTRAINT fk_expense_category FOREIGN KEY (expense_category_id) REFERENCES expense_categories (id),
    CONSTRAINT chk_year CHECK (year >= 2000 AND year <= 2100),
    CONSTRAINT chk_month CHECK (month >= 1 AND month <= 12),
    CONSTRAINT uq_expense_category_year_month UNIQUE (expense_category_id, year, month)
);
