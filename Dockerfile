ARG PHP_VERSION

FROM php:$PHP_VERSION-cli-alpine as base

RUN apk update && apk add libzip-dev
RUN docker-php-ext-install zip

WORKDIR /opt/package

COPY --from=composer:2.1 /usr/bin/composer /usr/bin/composer
COPY . /opt/package/

RUN chmod +x /opt/package/entrypoint.sh
ENTRYPOINT /opt/package/entrypoint.sh
