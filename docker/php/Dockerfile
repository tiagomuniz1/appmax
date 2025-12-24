FROM php:8.5-cli

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install pdo_mysql zip

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug


COPY docker/php/conf.d/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini


COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
