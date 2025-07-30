server-restart: stop start

stop:
	docker compose down -v --remove-orphans

start:
	docker compose up -d

down:
	docker compose down --remove-orphans

composer-install:
	docker compose run --rm game-php composer install

docker-up:
	docker compose up -d

docker-build:
	docker compose build --pull

docker-pull:
	docker compose pull
	
composer-update:
	docker compose run --rm game-php composer update

command-game-start:
	docker compose run --rm game-php php bin/console game:start

ws-restart:
	docker-compose restart game-websocket
