ARG PHP_VERSION=8.1.13-fpm
ARG COMPOSER_VERSION=2.4

FROM composer:${COMPOSER_VERSION} as composer
FROM php:${PHP_VERSION}

COPY --from=composer /usr/bin/composer /usr/local/bin/composer

ARG XDEBUG_VERSION=3.1.6
ARG INSTALL_XDEBUG=false

RUN if [ ${INSTALL_XDEBUG} = true ]; then \
    pecl install xdebug-${XDEBUG_VERSION} \
    && docker-php-ext-enable xdebug \
;fi

ARG PHPREDIS_VERSION=5.3.7

RUN apt-get update && apt-get install --assume-yes \
    libpcre3-dev \
    gnupg \
    libpq-dev \
    zip \
    libzip-dev \
    libpng-dev \
    && pecl install redis-${PHPREDIS_VERSION} \
    && docker-php-ext-install \
    pdo_pgsql \
    zip \
    gd \
    && docker-php-ext-configure \
    zip \
    && docker-php-ext-enable \
    redis

# Install Postgresql client (https://www.postgresql.org/download/linux/debian/)
RUN curl https://www.postgresql.org/media/keys/ACCC4CF8.asc | gpg --dearmour --output /etc/apt/trusted.gpg.d/apt.postgresql.org.gpg >/dev/null \
    && bash -c 'echo "deb http://apt.postgresql.org/pub/repos/apt bullseye-pgdg main" > /etc/apt/sources.list.d/pgdg.list' \
    && apt-get update \
    && apt-get install --assume-yes postgresql-client-15
