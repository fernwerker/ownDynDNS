FROM php:fpm-alpine
RUN apk add --no-cache libxml2-dev \
    && docker-php-ext-install soap
VOLUME /var/www/html