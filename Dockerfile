FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    git unzip zip curl gnupg libzip-dev libpng-dev libonig-dev libxml2-dev \
    libcurl4-openssl-dev libpq-dev nodejs npm \
    && docker-php-ext-install pdo pdo_mysql zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN a2enmod rewrite

WORKDIR /var/www/html
COPY . .

# Instalar dependencias
RUN composer install --optimize-autoloader --no-dev
RUN npm install && npm run build

# Cache de config
RUN php artisan config:cache
RUN php artisan route:cache

# Permisos
RUN chown -R www-data:www-data storage bootstrap/cache

ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

EXPOSE 80
CMD php artisan migrate --force && apache2-foreground
