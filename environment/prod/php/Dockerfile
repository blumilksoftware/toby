ARG PHP_VERSION=8.1.13-fpm
ARG NODE_VERSION=19-alpine3.16
ARG COMPOSER_VERSION=2.4

### PHP DEPENDENCIES ###
FROM composer:${COMPOSER_VERSION} as vendor

WORKDIR /app_composer_dependencies

COPY composer.json composer.lock ./

RUN composer install \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --no-dev \
    --prefer-dist \
    --ignore-platform-reqs

### FRONTEND ###
FROM node:${NODE_VERSION} as frontend

WORKDIR /app_frontend_dependencies

COPY package.json package-lock.json postcss.config.js tailwind.config.js vite.config.js webpack.config.js webpack.mix.js ./

RUN npm clean-install

COPY resources/js ./resources/js/
COPY resources/css ./resources/css/

RUN npm run build

### APPLICATION ###
FROM php:${PHP_VERSION} as prod

COPY --from=vendor /usr/bin/composer /usr/local/bin/composer

WORKDIR /application

ARG PHPREDIS_VERSION=5.3.7

RUN apt-get update && apt-get install --assume-yes \
    libpcre3-dev \
    gnupg \
    libpq-dev \
    zip \
    libzip-dev \
    libpng-dev \
    supervisor \
    cron \
    && pecl install redis-${PHPREDIS_VERSION} \
    && docker-php-ext-install \
    pdo_pgsql \
    zip \
    gd \
    opcache \
    && docker-php-ext-configure \
    zip \
    && docker-php-ext-enable \
    redis

ARG POSTGRESQL_CLIENT_VERSION=15

# Install Postgresql client (https://www.postgresql.org/download/linux/debian/)
RUN curl https://www.postgresql.org/media/keys/ACCC4CF8.asc | gpg --dearmour --output /etc/apt/trusted.gpg.d/apt.postgresql.org.gpg >/dev/null \
    && bash -c 'echo "deb http://apt.postgresql.org/pub/repos/apt bullseye-pgdg main" > /etc/apt/sources.list.d/pgdg.list' \
    && apt-get update \
    && apt-get install --assume-yes postgresql-client-${POSTGRESQL_CLIENT_VERSION}

# Copy Frontend build
COPY --chown=www-data:www-data --from=frontend /app_frontend_dependencies/node_modules ./node_modules/
COPY --chown=www-data:www-data --from=frontend /app_frontend_dependencies/public/build ./public/build/

# Copy Composer dependencies
COPY --chown=www-data:www-data --from=vendor /app_composer_dependencies/vendor ./vendor/

# Copy App files
COPY --chown=www-data:www-data . .

RUN composer dump-autoload --optimize
