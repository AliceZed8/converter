#!/bin/bash


cd symfony

#Устанавливаем симфонию
php composer.phar install

#Создаем БД при первом запуске
php bin/console doctrine:database:create --if-not-exists

#Запускаем миграции
php bin/console doctrine:migrations:migrate



#Запускаем PHP-FPM
php-fpm
