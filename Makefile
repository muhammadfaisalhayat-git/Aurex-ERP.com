# Aurex ERP Makefile
# Common commands for development and deployment

.PHONY: help install start stop restart logs shell migrate seed fresh test build deploy

# Default target
help:
	@echo "Aurex ERP - Available Commands:"
	@echo ""
	@echo "  make install    - Install dependencies and setup"
	@echo "  make start      - Start Docker containers"
	@echo "  make stop       - Stop Docker containers"
	@echo "  make restart    - Restart Docker containers"
	@echo "  make logs       - View container logs"
	@echo "  make shell      - Access app container shell"
	@echo "  make migrate    - Run database migrations"
	@echo "  make seed       - Seed database with demo data"
	@echo "  make fresh      - Fresh migrate and seed"
	@echo "  make test       - Run tests"
	@echo "  make build      - Build Docker images"
	@echo "  make deploy     - Deploy to production"
	@echo "  make clean      - Clean up containers and volumes"
	@echo ""

# Installation
install:
	@echo "Installing Aurex ERP..."
	composer install
	cp .env.example .env
	php artisan key:generate
	@echo "Installation complete! Configure .env and run 'make migrate'"

# Docker Commands
start:
	docker-compose up -d
	@echo "Aurex ERP started at http://localhost:8080"

stop:
	docker-compose down

restart:
	docker-compose restart

logs:
	docker-compose logs -f

shell:
	docker-compose exec app bash

# Database
migrate:
	docker-compose exec app php artisan migrate --force

seed:
	docker-compose exec app php artisan db:seed --force

fresh:
	docker-compose exec app php artisan migrate:fresh --seed --force

# Testing
test:
	docker-compose exec app php artisan test

# Build
build:
	docker-compose build --no-cache

# Deployment
deploy:
	./deploy.sh

# Cleanup
clean:
	docker-compose down -v
	docker system prune -f

# Production optimization
optimize:
	docker-compose exec app php artisan config:cache
	docker-compose exec app php artisan route:cache
	docker-compose exec app php artisan view:cache
	docker-compose exec app composer dump-autoload --optimize

# Clear caches
clear:
	docker-compose exec app php artisan config:clear
	docker-compose exec app php artisan route:clear
	docker-compose exec app php artisan view:clear
	docker-compose exec app php artisan cache:clear
