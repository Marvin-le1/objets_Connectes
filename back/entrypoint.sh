#!/bin/sh

# Attendre la DB
sleep 5

# Migrations + seed
php artisan migrate --force
php artisan db:seed --force

# Lancer le serveur Laravel
php artisan serve --host=0.0.0.0 --port=8000