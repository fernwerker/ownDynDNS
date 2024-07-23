FROM serversideup/php:8.3-fpm-nginx-alpine
USER root
RUN mkdir -p /var/www/html/src &&\
    install-php-extensions soap
USER www-data
COPY --chown=www-data:www-data ./default.conf /etc/nginx/conf.d/default.conf
COPY --chown=www-data:www-data ./data/src/ /var/www/html/src
COPY --chown=www-data:www-data ./data/update.php /var/www/html
COPY --chown=www-data:www-data ./data/.env.dist /var/www/html/.env
VOLUME /var/www/html
WORKDIR /var/www/html
EXPOSE 80