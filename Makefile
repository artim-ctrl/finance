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
