FROM php:8.3-fpm

WORKDIR /var/www/html

# Install GD extension
RUN apt-get update && apt-get install -y libfreetype6-dev libjpeg62-turbo-dev libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

RUN apt-get install -y supervisor nodejs npm

RUN docker-php-ext-install pdo pdo_mysql bcmath

# Copy supervisord configuration
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy entrypoint script
COPY entrypoint.sh /usr/local/bin/entrypoint.sh

COPY run-scheduler.sh /usr/local/bin/run-scheduler.sh

COPY run-delete-qr-code.sh /usr/local/bin/run-delete-qr-code.sh

RUN chmod +x /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/run-scheduler.sh
RUN chmod +x /usr/local/bin/delete-qr-code.sh

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html