# ---------- 1) vendor (php deps) ----------
FROM php:8.3-cli-alpine AS vendor
WORKDIR /app

# deps for intl/zip + git (composer fallback to source)
RUN apk add --no-cache git icu-dev libzip-dev zip unzip \
    && docker-php-ext-install intl zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts


# ---------- 2) node deps ----------
FROM node:20-alpine AS node_deps
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci


# ---------- 3) php_build (build assets + wayfinder) ----------
FROM php:8.3-fpm-alpine AS php_build
WORKDIR /var/www

RUN apk add --no-cache \
    bash curl git \
    icu-dev libzip-dev oniguruma-dev \
    libpng-dev libjpeg-turbo-dev freetype-dev \
    nodejs npm

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install pdo_mysql intl zip opcache gd

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# code
COPY . .

# чистим возможные локальные кеши (чтобы не тянул dev providers/старые пути)
RUN rm -f bootstrap/cache/packages.php bootstrap/cache/services.php bootstrap/cache/config.php bootstrap/cache/routes-v7.php || true

# vendor
COPY --from=vendor /app/vendor ./vendor

# node_modules
COPY --from=node_deps /app/node_modules ./node_modules

# writable dirs BEFORE artisan (без bash brace expansion!)
RUN mkdir -p \
    storage/framework/cache \
    storage/framework/data \
    storage/framework/sessions \
    storage/framework/testing \
    storage/framework/views \
    bootstrap/cache \
 && chown -R www-data:www-data storage bootstrap/cache

# minimal .env so artisan can boot (NO secrets)
RUN test -f .env || cp .env.example .env \
 && sed -i 's/^APP_ENV=.*/APP_ENV=production/' .env || true \
 && sed -i 's/^APP_DEBUG=.*/APP_DEBUG=false/' .env || true \
 && (grep -q '^APP_KEY=' .env && sed -i 's/^APP_KEY=.*/APP_KEY=base64:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA=/' .env || echo "APP_KEY=base64:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA=" >> .env)

# wayfinder for vite plugin
RUN php artisan wayfinder:generate --with-form -vvv

# build frontend (public/build)
RUN npm run build


# ---------- 4) runtime (final) ----------
FROM php:8.3-fpm-alpine AS runtime
WORKDIR /var/www

RUN apk add --no-cache \
    bash curl \
    icu-dev libzip-dev oniguruma-dev \
    libpng-dev libjpeg-turbo-dev freetype-dev \
    autoconf g++ make linux-headers

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install pdo_mysql intl zip opcache gd

# phpredis
RUN pecl install redis && docker-php-ext-enable redis

# copy built app (code + vendor + public/build)
COPY --from=php_build /var/www /var/www

# do not keep env inside image
RUN rm -f .env || true

# permissions
RUN mkdir -p \
    storage/framework/cache \
    storage/framework/data \
    storage/framework/sessions \
    storage/framework/testing \
    storage/framework/views \
    bootstrap/cache \
 && chown -R www-data:www-data storage bootstrap/cache

USER www-data
CMD ["php-fpm"]