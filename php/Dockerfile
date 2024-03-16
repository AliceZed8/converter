FROM php:8-fpm-alpine

RUN docker-php-ext-install pdo_mysql\
   && rm -rf /tmp/*


WORKDIR /var/www/app
EXPOSE 9000


ENTRYPOINT [ "sh", "run.sh" ]



