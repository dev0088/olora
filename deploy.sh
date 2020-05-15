#!/bin/sh

# Stop previous dockers
docker-compose stop
docker-compose down

# Run dockers as daemon
docker-compose up -d --build

# Generate key
docker-compose exec app php artisan key:generate

# Save config files
docker-compose exec app php artisan config:cache

# Migrate database
docker-compose exec app php artisan migrate

# Remove caches
yes | docker system prune


