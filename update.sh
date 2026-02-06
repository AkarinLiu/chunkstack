#!/bin/bash
git pull
npm install
npm run build
composer install
php artisan migrate
