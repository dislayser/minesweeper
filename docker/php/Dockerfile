FROM php:8.2-fpm-alpine

# Установка необходимых расширений
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install pcntl

# Установка зависимостей для Composer
RUN apk add --no-cache \
    bash \
    git \
    curl \
    unzip

# Установка Composer
RUN curl -sS https://getcomposer.org/installer  | php -- --install-dir=/usr/local/bin --filename=composer


# Создание директории для файлов
RUN mkdir -p /var/www/html/game
WORKDIR /app

# CMD ["php", "/app/game/Command/WS.php"]