DC = docker compose --env-file .env-docker

build: up update

up:
	$(DC) up -d

update:
	$(DC) exec -it backend /bin/sh -c 'composer install && php artisan key:generate'

test:
	$(DC) exec -it backend php artisan test

cs-fix:
	$(DC) exec -it backend composer lint

phpstan:
	$(DC) exec -it backend composer analyse

down:
	$(DC) down
