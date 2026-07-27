# Image dasar php versi CLI
FROM php:8.4-cli

# Install Depedensi sistem dasar dan ekstensi PHP
RUN apt-get update && apt-get install -y libzip-dev zip unzip && docker-php-ext-install pdo pdo_mysql zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer 

# Direktori kerja utama di dalam kontainer
WORKDIR /var/www