FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    git unzip zip curl gnupg libzip-dev libpng-dev libonig-dev libxml2-dev \
    libcurl4-openssl-dev libpq-dev nodejs npm \
    && docker-php-ext-install pdo pdo_mysql zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN a2enmod rewrite
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

WORKDIR /var/www/html
COPY . .

ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

RUN composer install --optimize-autoloader --no-dev \
 && npm install \
 && npm run build \
 && php artisan config:clear \
 && php artisan route:clear \
 && php artisan view:clear \
 && php artisan cache:clear \
 && php artisan config:cache \
 && php artisan route:cache \
 && php artisan migrate --force \
 && chown -R www-data:www-data storage bootstrap/cache

# 👇 NUEVO: Muestra el log de Laravel mientras arranca Apache
CMD tail -n 50 -f storage/logs/laravel.log & apache2-foreground
