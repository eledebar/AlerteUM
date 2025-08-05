FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    git unzip zip curl gnupg libzip-dev libpng-dev libonig-dev libxml2-dev \
    libcurl4-openssl-dev libpq-dev nodejs npm \
    && docker-php-ext-install pdo pdo_pgsql zip

RUN a2enmod rewrite

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . /var/www/html
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

WORKDIR /var/www/html
RUN composer install --optimize-autoloader --no-dev \
    && npm install \
    && npm run build

RUN chown -R www-data:www-data storage bootstrap/cache public

CMD php artisan config:clear && \
    php artisan migrate:fresh --seed --force && \
    apache2-foreground
