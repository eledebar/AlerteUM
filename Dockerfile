FROM php:8.2-apache

# Instalar dependencias necesarias del sistema
RUN apt-get update && apt-get install -y \
    git unzip zip curl gnupg libzip-dev libpng-dev libonig-dev libxml2-dev \
    libcurl4-openssl-dev libpq-dev nodejs npm \
    && docker-php-ext-install pdo pdo_mysql zip

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Habilitar mod_rewrite de Apache
RUN a2enmod rewrite

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Copiar el código fuente
COPY . .

# Configurar el document root de Apache para que apunte a /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

# Instalar dependencias y construir assets
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

# Puerto expuesto
EXPOSE 80

# Comando de inicio
CMD ["apache2-foreground"]
