ARG PHP_VERSION=8.1
ARG NODE_VERSION=19-alpine3.15
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

RUN npm install clean-install

COPY resources/js ./resources/js/
COPY resources/css ./resources/css/

RUN npm run build

### APPLICATION ###
FROM ghcr.io/blumilksoftware/php:${PHP_VERSION} as prod

WORKDIR /application

RUN apk add --no-cache pcre-dev $PHPIZE_DEPS \
    postgresql-dev \
    zip \
    libzip-dev \
    libpng-dev \
    supervisor \
    && pecl install redis \
    && docker-php-ext-install \
    pdo_pgsql \
    zip \
    gd \
    opcache \
    && docker-php-ext-configure \
    zip \
    && docker-php-ext-enable \
    redis

# Copy Frontend build
COPY --from=frontend /app_frontend_dependencies/node_modules ./node_modules/
COPY --from=frontend /app_frontend_dependencies/public/build ./public/build/

# Copy Composer dependencies
COPY --from=vendor /app_composer_dependencies/vendor ./vendor/

# Copy App files
COPY --chown=www-data:www-data . .

RUN composer dump-autoload
