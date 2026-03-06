# =====================================================================
# ERPGo SaaS — PHP 8.4 Application Container
# =====================================================================
# Multi-stage build for production-ready Laravel microservices
# =====================================================================

FROM php:8.4-fpm-alpine AS base

# Install system dependencies
RUN apk add --no-cache \
    bash \
    curl \
    git \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    freetype-dev \
    libzip-dev \
    zip \
    unzip \
    icu-dev \
    oniguruma-dev \
    mysql-client \
    supervisor \
    nginx \
    && rm -rf /var/cache/apk/*

# Install PHP extensions
RUN docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mysqli \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
        opcache

# Install Redis extension
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ── PHP Configuration ──────────────────────────────────────────────────
RUN { \
    echo "opcache.enable=1"; \
    echo "opcache.memory_consumption=256"; \
    echo "opcache.interned_strings_buffer=64"; \
    echo "opcache.max_accelerated_files=30000"; \
    echo "opcache.validate_timestamps=0"; \
    echo "opcache.save_comments=1"; \
    echo "opcache.fast_shutdown=1"; \
    } > /usr/local/etc/php/conf.d/opcache.ini

RUN { \
    echo "upload_max_filesize=50M"; \
    echo "post_max_size=50M"; \
    echo "memory_limit=512M"; \
    echo "max_execution_time=120"; \
    echo "max_input_vars=5000"; \
    } > /usr/local/etc/php/conf.d/custom.ini

# ── Nginx Configuration ───────────────────────────────────────────────
RUN mkdir -p /run/nginx
COPY docker/nginx-app.conf /etc/nginx/http.d/default.conf

# ── Supervisor Configuration ──────────────────────────────────────────
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# ── Application ────────────────────────────────────────────────────────
WORKDIR /var/www/html

# Copy composer files first for layer caching
COPY composer.json composer.lock* ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction 2>/dev/null || true

# Copy application code
COPY . .

# Generate autoloader & optimize
RUN composer dump-autoload --optimize --no-dev 2>/dev/null || true

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true

# Expose port 9000 for PHP-FPM and 8080 for Nginx
EXPOSE 9000 8080

# Start supervisor (manages both nginx + php-fpm)
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
