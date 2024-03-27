#!/bin/bash


cd symfony

#Устанавливаем симфонию
php composer.phar install

#Создаем БД и запускаем миграции при первом запуске
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate

#Аналогично для тестового окружения
php bin/console doctrine:database:create --if-not-exists --env=test
php bin/console doctrine:migrations:migrate --env=test


#Импортируем котировки
php bin/console app:import_quotes


#Запускаем PHP-FPM
php-fpm
