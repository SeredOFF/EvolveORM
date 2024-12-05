.DEFAULT_GOAL := up

check: test lint stan rector

ini: build up deps

build:
	@docker-compose build

up:
	$(info Make: starting containers)
	@docker-compose up -d

shut:
	$(info Make: stopped containers)
	@docker-compose down --remove-orphans

cli:
	$(info Make: exec command shell)
	@docker exec -it evolve-orm-php-cli bash

ls:
	$(info Make: show working and stoped containers)
	@docker ps -a

deps:
	$(info Make: install dependecies)
	@docker exec -it evolve-orm-php-cli composer install

test:
	$(info Make: start testing)
	@docker exec -it evolve-orm-php-cli vendor/bin/phpunit tests

lint:
	$(info Make: check lint)
	@docker exec -it evolve-orm-php-cli vendor/bin/phpcs

lint-fix:
	$(info Make: lint fix)
	@docker exec -it evolve-orm-php-cli vendor/bin/phpcbf

stan:
	$(info Make: analyse source code)
	@docker exec -it evolve-orm-php-cli vendor/bin/phpstan analyse

rector:
	@docker exec -it evolve-orm-php-cli vendor/bin/rector process --dry-run

rector-fix:
	@docker exec -it evolve-orm-php-cli vendor/bin/rector process
