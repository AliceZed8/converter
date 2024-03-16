#!/bin/bash

#Устанавливаем симфонию
cd symfony
php composer.phar install

#Создаем БД при первом запуске
php bin/console doctrine:database:create --if-not-exists

php-fpm
