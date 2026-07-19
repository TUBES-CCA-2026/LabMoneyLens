# syntax=docker/dockerfile:1

# =========================================================
# LabMoneyLens - Dockerfile
# PHP 8.3 FPM + Composer + Node.js (untuk build asset Vite)
# =========================================================

FROM composer:2 AS composer_bin

FROM php:8.3-fpm AS base

LABEL maintainer="LabMoneyLens"
LABEL description="PHP-FPM 8.3 runtime untuk LabMoneyLens (Laravel 13)"

WORKDIR /var/www/html

# -----------------------------------------------------------------
# 1. Install dependency sistem yang dibutuhkan untuk extension PHP
#    dan tools pendukung (git, unzip untuk composer, mysql-client
#    untuk wait-for-db & migrate, curl untuk NodeSource setup).
# -----------------------------------------------------------------
RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        unzip \
        curl \
        ca-certificates \
        gnupg \
        default-mysql-client \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libonig-dev \
        libxml2-dev \
        libzip-dev \
        libicu-dev \
    && rm -rf /var/lib/apt/lists/*

# -----------------------------------------------------------------
# 2. Install Node.js 20 LTS (dibutuhkan untuk `npm run build` Vite).
#    Tidak pakai nodejs bawaan Debian karena versinya terlalu lama
#    untuk laravel-vite-plugin/vite versi terbaru.
# -----------------------------------------------------------------
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    && rm -rf /var/lib/apt/lists/*

# -----------------------------------------------------------------
# 3. Konfigurasi & compile extension GD (butuh flag jpeg/freetype)
#    lalu install seluruh extension PHP yang dipakai project.
# -----------------------------------------------------------------
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql \
        mbstring \
        bcmath \
        exif \
        pcntl \
        gd \
        zip \
        intl \
        opcache

# -----------------------------------------------------------------
# 4. Copy binary Composer resmi (bukan curl script installer).
# -----------------------------------------------------------------
COPY --from=composer_bin /usr/bin/composer /usr/bin/composer

# -----------------------------------------------------------------
# 5. Salin konfigurasi PHP tambahan (upload size, opcache, dsb).
# -----------------------------------------------------------------
COPY docker/php/local.ini /usr/local/etc/php/conf.d/local.ini

# -----------------------------------------------------------------
# 6. Salin & siapkan entrypoint script.
# -----------------------------------------------------------------
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# -----------------------------------------------------------------
# 7. Composer & npm cache directory milik www-data, supaya proses
#    install di dalam container (sebagai www-data) tidak butuh root.
# -----------------------------------------------------------------
RUN mkdir -p /var/www/.composer /var/www/.npm \
    && chown -R www-data:www-data /var/www

USER www-data

EXPOSE 9000

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
