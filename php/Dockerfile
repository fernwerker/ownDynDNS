FROM php:fpm-alpine
RUN apk add --no-cache libxml2 \
    && docker-php-ext-install soap
VOLUME /var/www/html