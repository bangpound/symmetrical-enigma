FROM hashicorp/envconsul:alpine AS envconsul

FROM php:7.1-fpm-alpine

COPY --from=envconsul /bin/envconsul /bin/envconsul

RUN apk add --no-cache $PHPIZE_DEPS && \
    docker-php-ext-install opcache && \
    pecl install apcu && \
    docker-php-ext-enable apcu && \
    pecl install xdebug && \
    docker-php-ext-enable xdebug
