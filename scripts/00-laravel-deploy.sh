#!/usr/bin/env bash
echo "Running composer..."
composer install --no-dev --working-dir=/var/www/html

echo "Flush old cache..."
php artisan cache:clear

echo "Caching config..."
php artisan config:cache

echo "Caching routes..."
php artisan route:cache

echo "Caching events..."
php artisan event:cache

echo "Running migrations..."
php artisan migrate:fresh --force

echo "Seed db..."
php artisan db:seed --force
