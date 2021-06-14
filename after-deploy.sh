#!/bin/bash

if [ -f /.dockerenv ]; then
    echo "I'm inside matrix (docker container)...";
    # so update everything necessary
    chmod -R a+w storage/framework/
    composer install --ignore-platform-reqs
    composer dumpautoload
    rm bootstrap/cache/config.php
    php artisan config:cache
    php artisan cache:clear
    php artisan migrate
    php artisan passport:client --personal -n
    supervisorctl restart all
else
    echo "I'm living in real world (host OS)...";
    # run self but inside docker container
    docker-compose exec -T app sh after-deploy.sh
fi



