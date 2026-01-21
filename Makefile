mkfile_path := $(abspath $(lastword $(MAKEFILE_LIST)))
current_dir := $(dir $(mkfile_path))

COMPOSE=docker-compose -p chat -f docker-compose.yml --env-file $(current_dir)docker/.env.local

app.create.admin:
	$(COMPOSE) exec php-fpm php bin/console app:create-admin admin@admin.admin 12345678

app.rebuild: docker.rebuild composer.install yarn.install yarn.build

app.start.dev: app.rebuild yarn.dev db.reload

app.start.prod: app.rebuild

app.start.first: app.rebuild
	$(COMPOSE) exec php-fpm php bin/console doctrine:database:create
	$(COMPOSE) exec php-fpm php bin/console doctrine:schema:create
	#$(COMPOSE) exec php-fpm php bin/console doctrine:fixtures:load -n --group=UserFixtures

composer.install:
	$(COMPOSE) exec php-fpm composer install

db.migrate:
	$(COMPOSE) exec php-fpm php bin/console doctrine:migrations:migrate -n

db.reload:
	$(COMPOSE) exec php-fpm php bin/console doctrine:database:drop --force
	$(COMPOSE) exec php-fpm php bin/console doctrine:database:create
	$(COMPOSE) exec php-fpm php bin/console doctrine:schema:create
	$(COMPOSE) exec php-fpm php bin/console doctrine:fixtures:load --append
	$(COMPOSE) exec php-fpm php bin/console app:create-admin admin@admin.admin 12345678

docker.build: docker.stop
	$(COMPOSE) build --no-cache

docker.rebuild: docker.stop
	$(COMPOSE) up -d --build

docker.remove:
	$(COMPOSE) down --remove-orphans

docker.restart: docker.stop docker.start

docker.start:
	$(COMPOSE) up -d

docker.stop:
	$(COMPOSE) down

shell.mysql:
	$(COMPOSE) exec mysql bash

shell.nginx:
	$(COMPOSE) exec nginx sh

shell.php:
	$(COMPOSE) exec php-fpm sh

yarn.build:
	$(COMPOSE) exec php-fpm yarn build

yarn.dev:
	$(COMPOSE) exec php-fpm yarn dev

yarn.install:
	$(COMPOSE) exec php-fpm yarn install

yarn.watch:
	$(COMPOSE) exec php-fpm yarn watch
