FROM php:8.3-fpm

# Install necessary packages and PHP extensions
RUN apt-get update \
    && apt-get install -y nano zip unzip git libicu-dev supervisor nodejs npm libpng-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl mysqli pdo pdo_mysql gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html

COPY docker/supervisor/supervisord.conf /etc/supervisor/supervisord.conf

# Set permissions and install Composer dependencies
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && git config --global --add safe.directory /var/www/html \
    && composer install

EXPOSE 9000

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]

ENTRYPOINT [ "docker/entrypoint.sh" ]
