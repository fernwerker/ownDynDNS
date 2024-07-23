FROM serversideup/php:8.3-fpm-nginx-alpine
USER root
RUN mkdir -p /var/www/html/public/src &&\
    install-php-extensions soap
USER www-data
WORKDIR /var/www/html/public
COPY --chown=www-data:www-data ./data/src/ /var/www/html/public/src
COPY --chown=www-data:www-data ./data/update.php /var/www/html/public
COPY --chown=www-data:www-data ./data/.env.dist /var/www/html/public/.env
HEALTHCHECK --interval= --timeout=5s --start-period=10s CMD curl --insecure --silent --location --show-error --fail http://localhost:8080$HEALTHCHECK_PATH || exit 1
