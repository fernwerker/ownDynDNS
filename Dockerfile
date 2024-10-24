FROM serversideup/php:8.3-fpm-nginx-alpine
USER root
RUN mkdir -p /make-executable
COPY --chown=root:root data/etc/ /etc
RUN mkdir -p /var/www/html/public/src &&\
    install-php-extensions soap
USER www-data
WORKDIR /var/www/html/public
COPY --chown=www-data:www-data ./data/src/ /var/www/html/public/src
COPY --chown=www-data:www-data ./data/update.php /var/www/html/public
COPY --chown=www-data:www-data ./data/.env.dist /var/www/html/public/.env
RUN sed -i "s|listen \[::\]:8080 default_server;|# \0|" /etc/nginx/site-opts.d/http.conf.template
HEALTHCHECK --interval= --timeout=5s --start-period=10s CMD curl --insecure --silent --location --show-error --fail http://localhost:8080$HEALTHCHECK_PATH || exit 1
LABEL org.opencontainers.image.source=https://github.com/niiwiicamo/owndyndns
LABEL org.opencontainers.image.description="OwnDynDNS Docker image, used to interface with the netcup hosting providers domain API and provide an endpoint for DynDNS updates by consumer routers."
LABEL org.opencontainers.image.version=v1.1
LABEL org.opencontainers.image.title="OwnDynDNS"
LABEL org.opencontainers.image.base.name="docker.io/serversideup/php:8.3-fpm-nginx-alpine"
LABEL org.opencontainers.image.authors="Nils Blume, github.com/NiiWiiCamo"
