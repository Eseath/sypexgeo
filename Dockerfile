FROM php:7.1-cli-alpine

RUN apk update && apk add libzip-dev
RUN docker-php-ext-install zip

COPY --from=composer:2.1.12 /usr/bin/composer /usr/bin/composer
RUN composer self-update
RUN composer --version

WORKDIR /opt/package