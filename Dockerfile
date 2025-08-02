# Étape 1 : Construire l'application avec PHP et Composer
FROM php:8.2-cli AS build

# Installer les dépendances système
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    && docker-php-ext-install pdo_mysql zip

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier les fichiers du projet
WORKDIR /app
COPY . .

# Installer les dépendances Laravel
RUN composer install --no-dev --optimize-autoloader

# Étape 2 : Exécuter l'application
FROM php:8.2-cli

# Installer les dépendances système
RUN apt-get update && apt-get install -y \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    && docker-php-ext-install pdo_mysql zip

# Copier l'application depuis le build précédent
COPY --from=build /app /app

# Définir le répertoire de travail
WORKDIR /app

# Définir les variables d'environnement
ENV PORT=8080
EXPOSE 8080

# Donner les bons droits
RUN chmod -R 755 /app

# Lancer les migrations et le serveur Laravel
CMD php artisan config:clear && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=${PORT}
