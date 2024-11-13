FROM php:8.3-fpm

WORKDIR /var/www/html

# Install GD extension
RUN apt-get update && apt-get install -y libfreetype6-dev libjpeg62-turbo-dev libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

RUN docker-php-ext-install pdo pdo_mysql bcmath

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html