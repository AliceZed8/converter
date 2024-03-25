FROM php:8-fpm-alpine

RUN apk add --no-cache curl git zlib-dev build-base autoconf bash

#Установка PDO
RUN docker-php-ext-install pdo_mysql && \
    rm -rf /tmp/*
RUN docker-php-ext-enable pdo_mysql


#Установка XDEBUG
RUN apk add --update linux-headers
RUN pecl install xdebug && docker-php-ext-enable xdebug

WORKDIR /var/www/app
EXPOSE 9000


ENTRYPOINT [ "sh", "run.sh" ]



