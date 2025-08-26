# Reading List Project Makefile

# variables
DOCKER_COMPOSE = docker compose
LARAVEL_CONTAINER = laravel
EXEC_LARAVEL = $(DOCKER_COMPOSE) exec $(LARAVEL_CONTAINER)

# default target
.DEFAULT_GOAL := help

help: ## show available commands
	@echo "Available commands:"
	@echo "  help       show available commands"
	@echo "  up         start docker containers"
	@echo "  down       stop docker containers"
	@echo "  clear      clear all laravel caches"
	@echo "  shell      enter laravel container shell"
	@echo "  logs       show laravel logs"
	@echo "  test       run laravel tests"

up:
	$(DOCKER_COMPOSE) up -d

down:
	$(DOCKER_COMPOSE) down

clear:
	@echo "clearing laravel caches..."
	$(EXEC_LARAVEL) sh -c "php artisan optimize:clear"
	$(EXEC_LARAVEL) sh -c "php artisan cache:clear"
	$(EXEC_LARAVEL) sh -c "php artisan config:clear"
	$(EXEC_LARAVEL) sh -c "php artisan route:clear"
	$(EXEC_LARAVEL) sh -c "php artisan view:clear"
	$(EXEC_LARAVEL) sh -c "php artisan event:clear"
	$(EXEC_LARAVEL) sh -c "composer dump-autoload"
	@echo "all caches cleared"

shell:
	$(EXEC_LARAVEL) sh

logs:
	$(EXEC_LARAVEL) sh -c "tail -f storage/logs/laravel.log"

test:
	$(EXEC_LARAVEL) sh -c "./vendor/bin/phpunit"

.PHONY: help up down clear shell logs
