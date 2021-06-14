#!/bin/bash
if [ -f /.dockerenv ]; then
    echo "I'm inside matrix ;(";
    touch ./storage/logs/laravel.log ./storage/logs/laravel-worker.log
    tail -f /var/www/app/storage/logs/laravel.log /var/www/app/storage/logs/laravel-worker.log /var/log/nginx/error.log /var/log/nginx/access.log # | ccze -A
else
    echo "I'm living in real world!";
    docker-compose exec app bash logs.sh
fi



