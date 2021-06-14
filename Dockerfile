FROM cookiebinary/laravel-full-stack:php8.0
RUN apt update && apt install -y iputils-ping mysql-client htop curl redis-server php-redis ccze
RUN curl -sL https://deb.nodesource.com/setup_14.x | bash -
RUN apt-get install -y nodejs
COPY . /var/www/app
WORKDIR /var/www/app
RUN chgrp -R www-data storage bootstrap/cache
RUN chmod -R ug+rwx storage bootstrap/cache
#RUN composer install --ignore-platform-reqs
RUN php artisan config:cache
COPY .bashrc /root
CMD sh /var/www/app/start.sh
