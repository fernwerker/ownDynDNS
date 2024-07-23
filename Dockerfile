FROM php:fpm-alpine
RUN apk add --no-cache nginx libxml2-dev \
    && docker-php-ext-install soap \
    && mkdir -p /var/www/html/src
COPY ./default.conf /etc/nginx/http.d/default.conf
COPY ./data/src/ /var/www/html/src
COPY ./data/update.php /var/www/html
COPY ./data/.env.dist /var/www/html/.env
VOLUME /var/www/html
WORKDIR /var/www/html
EXPOSE 80