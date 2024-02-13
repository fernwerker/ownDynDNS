FROM nginx:alpine
RUN mkdir -p /var/www/html/src
COPY ./nginx/default.conf /etc/nginx/conf.d
COPY ./data/src/ /var/www/html/src
COPY ./data/update.php /var/www/html
COPY ./data/.env.dist /var/www/html/.env
VOLUME /var/www/html
