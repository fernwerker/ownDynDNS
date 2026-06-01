FROM php:8.2-apache

WORKDIR /var/www/html

# Install SOAP extension for netcup DNS API integration
RUN apt-get update \
    && apt-get install -y --no-install-recommends libxml2-dev \
    && docker-php-ext-install soap \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache rewrite if needed by .htaccess
RUN a2enmod rewrite

# Copy application files
COPY . /var/www/html/

# Ensure proper permissions for Apache
RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type d -exec chmod 755 {} + \
    && find /var/www/html -type f -exec chmod 644 {} +

EXPOSE 80

CMD ["apache2-foreground"]
