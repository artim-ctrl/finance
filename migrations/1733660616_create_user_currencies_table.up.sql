CREATE TABLE user_currencies
(
    id       BIGSERIAL PRIMARY KEY,
    user_id  BIGINT UNIQUE NOT NULL,
    currency varchar(3)    NOT NULL,
    CONSTRAINT fk_user FOREIGN KEY (user_id) REFERENCES users (id)
);
