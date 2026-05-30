# ── Stage 1: PHP dependencies ─────────────────────────────────────────────────
FROM composer:2.7.6 AS vendor

WORKDIR /app
COPY composer.json composer.lock ./

ENV COMPOSER_MEMORY_LIMIT=-1

RUN composer install \
    --optimize-autoloader \
    --no-dev \
    --no-scripts \
    --no-interaction \
    --prefer-dist \
    --ignore-platform-reqs


# ── Stage 2: Runtime (PHP-FPM + nginx) ────────────────────────────────────────
FROM php:8.2-fpm-alpine

# System deps + PHP extensions (cached unless Dockerfile changes)
RUN apk add --no-cache \
        nginx \
        supervisor \
        curl \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        libzip-dev \
        oniguruma-dev \
        icu-dev \
        file \
        ca-certificates \
        openssl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        bcmath \
        mbstring \
        opcache \
        gd \
        zip \
        exif \
        pcntl \
        intl \
    && update-ca-certificates \
    && rm -rf /var/cache/apk/*

WORKDIR /var/www/html

# Copy docker configs early (cached unless docker/ changes)
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Copy vendor from stage 1 (cached unless composer.lock changes)
COPY --from=vendor /app/vendor ./vendor

# Copy app code last (busts cache on every deploy, but everything above is cached)
COPY . .

# Permissions
RUN touch /var/www/html/.env \
    && mkdir -p storage/app/public \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        bootstrap/cache \
        /run/nginx \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 storage bootstrap/cache

EXPOSE 8080

HEALTHCHECK --interval=30s --timeout=5s --start-period=60s --retries=3 \
    CMD curl -f http://127.0.0.1:8080/health || exit 1

ENTRYPOINT ["/entrypoint.sh"]
