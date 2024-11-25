FROM php:8.3-apache

WORKDIR /var/www/html

# Install necessary extensions
RUN apt-get update && apt-get install -y libfreetype6-dev libjpeg62-turbo-dev libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql pcntl

EXPOSE 4080

CMD [ "php", "artisan", "reverb:start", "--host=0.0.0.0" ]