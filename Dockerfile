FROM composer:latest AS vendor

WORKDIR /app

COPY . /app

RUN composer install --no-dev --prefer-dist --optimize-autoloader

FROM php:8.2-cli-alpine

RUN apk add --no-cache \
    php82-pdo \
    php82-pdo_mysql \
    php82-mbstring \
    php82-tokenizer \
    php82-xml \
    php82-ctype \
    php82-curl \
    php82-dom \
    php82-fileinfo \
    php82-openssl \
    php82-json \
    php82-phar \
    php82-posix \
    php82-zlib \
    php82-mysqli \
    php82-session \
    php82-simplexml \
    php82-xmlwriter \
    php82-mbstring \
    php82-bcmath \
    php82-gd \
    php82-intl \
    php82-pecl-redis \
    php82-pcntl \
    php82-opcache \
    curl \
    unzip \
    git \
    supervisor \
    bash \
    mysql-client

WORKDIR /app

COPY --from=vendor /app /app
COPY . /app

RUN chmod -R 755 /app \
 && chown -R www-data:www-data /app

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV APP_ENV=production

EXPOSE 8080

CMD php artisan config:clear \
 && php artisan migrate:fresh --seed --force \
 && php artisan serve --host=0.0.0.0 --port=8080
