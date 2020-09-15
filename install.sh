#!/usr/bin/env bash

PASSWORD=$1;
VERSION=$2;
# Pull the latest changes from the git repository
# git reset --hard
# git clean -df
if [ ! -z ${VERSION} ]
then
    git pull origin master
    git checkout ${VERSION}
fi

# Install/update composer dependecies
php composer.phar install --no-interaction --prefer-dist --optimize-autoloader --no-dev

mysql --user=root --password=${PASSWORD} -e "CREATE DATABASE laravel_angular_starter"

# Create application key
sudo php artisan key:generate

# Run database migrations
php artisan migrate --force

# Install passport
php artisan passport:install

# Seed db
php artisan db:seed

# Clear caches
php artisan cache:clear

# Clear expired password reset tokens
php artisan auth:clear-resets

# Clear and cache routes
php artisan route:clear
php artisan route:cache

# Clear and cache config
php artisan config:clear
php artisan config:cache

# Install node modules
npm install

# Build assets using Laravel Mix
npm run production
