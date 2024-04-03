FROM php:8.3-fpm-bookworm

RUN apt-get update \
    && apt-get install -y libicu-dev libpq5 libpq-dev zlib1g-dev libpng-dev libzip-dev zip \
    && docker-php-ext-configure intl \
    && docker-php-ext-install opcache intl gd zip \
    && pecl install apcu xdebug \
    && docker-php-ext-enable apcu xdebug \
    && pecl clear-cache

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ENV PATH=$PATH:/root/composer/vendor/bin COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /var/www/html

EXPOSE 9000
