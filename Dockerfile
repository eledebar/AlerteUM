FROM php:8.2-fpm-alpine

WORKDIR /var/www

RUN apk add --no-cache \
    bash \
    curl \
    git \
    unzip \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    freetype-dev \
    icu-dev \
    zlib-dev \
    oniguruma-dev \
    mysql-client \
    libzip-dev \
    supervisor

# Extensiones PHP necesarias
RUN docker-php-ext-install pdo pdo_mysql intl mbstring zip exif pcntl

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer install --no-dev --prefer-dist --optimize-autoloader

RUN chmod -R 755 /var/www && chown -R www-data:www-data /var/www

EXPOSE 8080

CMD php artisan config:clear \
 && php artisan migrate:fresh --seed --force \
 && php artisan serve --host=0.0.0.0 --port=8080
