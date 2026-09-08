# Image dasar php versi CLI
FROM php:8.4-cli

# Install Depedensi sistem dasar, ekstensi PHP, serta Node.js dan NPM
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    nodejs \
    npm \
    && docker-php-ext-install pdo pdo_mysql zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Direktori kerja utama di dalam kontainer
WORKDIR /var/www

# Salin file konfigurasi dependensi (untuk memanfaatkan Docker cache)
COPY composer.json composer.lock* package.json package-lock.json* ./

# Jalankan instalasi dependensi
RUN composer install --no-scripts --no-autoloader
RUN npm install

# Salin seluruh sisa file proyek ke dalam kontainer
COPY . .

# Generate autoloader Composer dan build aset frontend
RUN composer dump-autoload --optimize
RUN npm run build