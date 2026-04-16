FROM php:8.3-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    icu-dev \
    libzip-dev \
    libsodium-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install \
    intl \
    pdo_mysql \
    zip \
    sodium \
    sysvsem \
    opcache

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy composer files first for better caching (lock file for reproducible builds)
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copy application code
COPY . .

# Generate autoloader and run scripts
RUN composer dump-autoload --optimize \
    && composer run-script post-install-cmd || true

# Set permissions
RUN chown -R www-data:www-data var/ public/

EXPOSE 9000
CMD ["php-fpm"]
