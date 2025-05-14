FROM php:8.2-fpm-alpine

# Установка необходимых расширений
RUN docker-php-ext-install pdo_mysql

# Создание директории для файлов
RUN mkdir -p /var/www/html/game
WORKDIR /var/www/html/game