SERVER ?= symfony-php
USER := $(shell whoami)
PHP ?= php-fpm

d-build:
	docker compose build --no-cache $(PHP)

d-up:
	docker-compose up -d

d-start:
	docker-compose start

d-stop:
	docker-compose stop

d-ps:
	docker-compose ps

d-down:
	docker-compose down

d-remove:
	docker-compose stop
	docker system prune
	docker system prune -a --volumes

d-migrate:
	docker exec -it $(SERVER) bash -c "php bin/console doctrine:migrations:migrate"

fix-rights:
	sudo chown -R $(USER) .

fix-sock:
	sudo chown $(USER) /var/run/docker.sock

run-inside:
	docker exec -it $(SERVER) bash

phpunit:
	docker exec -it $(SERVER) bash -c "php bin/phpunit --testdox --filter=VideoWebTest"

composer-diagnose:
	docker exec -it --user=1000 $(SERVER) bash -c "composer diagnose"

composer-self-update:
	docker exec -it --user=1000 $(SERVER) bash -c "composer self-update --update-keys"

composer-install:
	docker exec -it --user=1000 $(SERVER) bash -c "composer install"

composer-update:
	docker exec -it --user=1000 $(SERVER) bash -c "composer update"

clear-cache:
	docker exec -it --user=1000 $(SERVER) bash -c "php bin/console c:c"

clear-cache-test:
	docker exec -it --user=1000 $(SERVER) bash -c "php bin/console cache:clear --env=test"

containers:
	docker ps --format "table {{.Names}}\t{{.Image}}\t{{.Networks}}"