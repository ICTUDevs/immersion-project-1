FROM php:8.3-fpm

WORKDIR /var/www/html

# Install GD extension
RUN apt-get update && apt-get install -y \
        nodejs npm git \
        build-essential \
        libpng-dev \
        libjpeg-dev \
        libwebp-dev \
        libxpm-dev \
        libfreetype6-dev \
        libzip-dev \
        zip \
        unzip \
        git \
        bash \
        fcgiwrap \
        libmcrypt-dev \
        libonig-dev \
        libpq-dev \
        supervisor \
        && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install gd pdo pdo_mysql mbstring zip exif pcntl bcmath opcache

RUN docker-php-ext-install pcntl
RUN docker-php-ext-configure pcntl --enable-pcntl

# Copy supervisord configuration
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy entrypoint script
COPY entrypoint.sh /usr/local/bin/entrypoint.sh

COPY run-scheduler.sh /usr/local/bin/run-scheduler.sh

COPY run-delete-qr-code.sh /usr/local/bin/run-delete-qr-code.sh

RUN chmod +x /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/run-scheduler.sh
RUN chmod +x /usr/local/bin/run-delete-qr-code.sh

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN git config --global --add safe.directory /var/www/html/vendor/laravel/framework

# Set correct file permissions
RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && find /var/www/html -type f -exec chmod 644 {} \;

COPY src/storage /var/www/html/storage
COPY src/bootstrap/cache /var/www/html/bootstrap/cache
COPY src/public /var/www/html/public

# Ensure the web server user has write permissions to storage and cache directories
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Set correct file permissions for public directory
RUN chown -R www-data:www-data /var/www/html/public \
    && chmod -R 755 /var/www/html/public

EXPOSE 9000

CMD ["php-fpm"]