FROM php:7.1-fpm-alpine

RUN apk add --no-cache $PHPIZE_DEPS && \
    docker-php-ext-install opcache && \
    pecl install apcu && \
    docker-php-ext-enable apcu && \
    pecl install xdebug && \
    docker-php-ext-enable xdebug
