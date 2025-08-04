FROM php:8.2-apache

# Instalar dependencias
RUN apt-get update && apt-get install -y \
    git unzip zip curl gnupg libzip-dev libpng-dev libonig-dev libxml2-dev \
    libcurl4-openssl-dev libpq-dev nodejs npm \
    && docker-php-ext-install pdo pdo_mysql zip

# Activar mod_rewrite
RUN a2enmod rewrite

# Copiar composer desde la imagen oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar código y configurar Apache
COPY . /var/www/html
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

# Instalar dependencias y compilar assets
WORKDIR /var/www/html
RUN composer install --optimize-autoloader --no-dev \
    && npm install \
    && npm run build

# Permisos
RUN chown -R www-data:www-data storage bootstrap/cache public

# Migraciones + seeds
CMD php artisan migrate:fresh --seed --force && apache2-foreground
