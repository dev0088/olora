#!/bin/sh

# Stop previous dockers
docker-compose stop
docker-compose down

# Copy env file
FILE=.env
if test -f "$FILE"; then
    echo "$FILE exist"
    cp .env.example .env
fi

# Run dockers as daemon
docker-compose up -d --build

# Install composer
# docker-compose exec app composer require doctrine/dbal
docker-compose exec app composer install

# Generate key
yes | docker-compose exec app php artisan key:generate

# Save config files
yes | docker-compose exec app php artisan config:cache

# Migrate database
yes | docker-compose exec app php artisan migrate

# Remove caches
yes | docker system prune