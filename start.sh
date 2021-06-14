#!/bin/bash

redis-server & echo "redis started."
cd /var/www/app
mkdir -p /var/www/app/storage/logs
mkdir -p /var/www/app/bootstrap/cache
touch /var/www/app/storage/logs/laravel.log
chgrp -R www-data storage bootstrap/cache
chmod -R ug+rwx storage bootstrap/cache
sh after-deploy.sh
php artisan config:cache
php artisan migrate --force
service supervisor start
service nginx start
service php8.0-fpm start
service cron start
php artisan websockets:serve & \
tail -f /var/www/app/storage/logs/laravel-* /var/log/nginx/access.log /var/log/nginx/error.log
