DC = docker compose --env-file .env-docker

build: up update

up:
	$(DC) up -d

update:
	$(DC) exec -it app /bin/sh -c 'composer install && npm install && php artisan key:generate'

test:
	$(DC) exec -it app php artisan test

cs-fix:
	$(DC) exec -it app /bin/sh -c 'composer lint && npm run prettier'

phpstan:
	$(DC) exec -it app composer analyse

down:
	$(DC) down
