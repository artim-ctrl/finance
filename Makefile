up:
	docker compose up -d

down:
	docker compose down

eslint:
	docker compose exec frontend /bin/sh -c 'yarn lint'

prettier:
	docker compose exec frontend /bin/sh -c 'yarn format'

yarn-build:
	docker compose exec frontend /bin/sh -c 'yarn build'

migration: # (use option name=migration_name)
	@echo "\033[32mCreating migration files\033[39m"
	touch ./migrations/`date +%s`_$(name).down.sql
	touch ./migrations/`date +%s`_$(name).up.sql
