server-restart:
	docker compose down -v --remove-orphans
	docker compose up -d

down:
	docker compose down --remove-orphans

start:
	docker compose up -d

composer-install:
	docker compose run --rm game-php composer install

docker-up:
	docker compose up -d

docker-build:
	docker compose build --pull

docker-pull:
	docker compose pull

docker-down-clear:
	docker compose down -v --remove-orphans
	
composer-update:
	docker compose run --rm game-php composer update
