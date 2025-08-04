FROM php:8.2-apache

# Dependencias necesarias
RUN apt-get update && apt-get install -y \
    git unzip zip curl gnupg libzip-dev libpng-dev libonig-dev libxml2-dev \
    libcurl4-openssl-dev libpq-dev nodejs npm \
    && docker-php-ext-install pdo pdo_mysql zip

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Habilitar mod_rewrite de Apache
RUN a2enmod rewrite

# Copiar código
COPY . /var/www/html

# Cambiar root del servidor web
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

WORKDIR /var/www/html

# Instalar dependencias PHP y Node
RUN composer install --optimize-autoloader --no-dev \
 && npm install \
 && npm run build \
 && php artisan config:clear \
 && php artisan route:clear \
 && php artisan view:clear \
 && php artisan config:cache \
 && php artisan route:cache \
 && php artisan migrate --force \
 && chown -R www-data:www-data storage bootstrap/cache

# Lanzar Apache en primer plano
CMD ["apache2-foreground"]
