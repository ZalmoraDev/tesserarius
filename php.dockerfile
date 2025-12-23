FROM php:8.4-fpm

# Install dependencies for PostgreSQL PDO
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Intall Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

CMD ["php-fpm"]