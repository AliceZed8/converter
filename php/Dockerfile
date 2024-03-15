FROM php:8-fpm-alpine

RUN docker-php-ext-install pdo_mysql\
   && rm -rf /tmp/*

CMD ["php-fpm", "-F"]

WORKDIR /var/www

EXPOSE 9000