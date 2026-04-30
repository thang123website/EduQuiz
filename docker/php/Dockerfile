FROM php:8.4-fpm

# Cài đặt các extension cơ bản cho Laravel và cấu hình OPcache
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libwebp-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd opcache

# Cài đặt Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy file cấu hình OPcache vào container
COPY ./docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY ./docker/php/custom.ini /usr/local/etc/php/conf.d/custom.ini
