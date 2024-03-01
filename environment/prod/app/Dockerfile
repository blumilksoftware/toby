ARG PHP_VERSION=8.3.0
ARG PHP_MODULE_NAME=php${PHP_VERSION}
# https://github.com/nginx/unit/tags
ARG UNIT_VERSION=1.31.1-1

### PHP DEPENDENCIES ###
FROM composer:2.7.1 as vendor

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
FROM node:21.6.1-bullseye-slim as frontend

WORKDIR /app_frontend_dependencies

COPY package.json package-lock.json postcss.config.js tailwind.config.js vite.config.js ./

RUN npm clean-install

COPY resources/js ./resources/js/
COPY resources/css ./resources/css/

RUN npm run build

### NGINIX UNIT PHP MODULE ###
FROM php:${PHP_VERSION}-cli-bullseye as unit-modules-builder

RUN apt-get update && apt-get install --assume-yes \
        git \
        libpcre2-dev

ARG UNIT_VERSION
ARG PHP_MODULE_NAME

RUN git clone https://github.com/nginx/unit \
        && cd unit \
        && git checkout ${UNIT_VERSION} \
        && ./configure \
        && ./configure php --module=${PHP_MODULE_NAME} \
        && make ${PHP_MODULE_NAME}


### APPLICATION ###
FROM php:${PHP_VERSION}-cli-bullseye as app

ARG UNIT_VERSION
# https://packages.nginx.org/unit/debian/pool/unit/u/unit/
ARG NGINX_UNIT_VERSION="${UNIT_VERSION}~bullseye"
ARG PHP_MODULE_NAME

COPY --from=unit-modules-builder /unit/build/lib/unit/modules/${PHP_MODULE_NAME}.unit.so /usr/lib/unit/modules/${PHP_MODULE_NAME}.unit.so

# Install Nginx Unit
RUN curl --output /usr/share/keyrings/nginx-keyring.gpg https://unit.nginx.org/keys/nginx-keyring.gpg \
    && echo 'deb [signed-by=/usr/share/keyrings/nginx-keyring.gpg] https://packages.nginx.org/unit/debian/ bullseye unit' | tee --append /etc/apt/sources.list.d/unit.list \
    && echo 'deb-src [signed-by=/usr/share/keyrings/nginx-keyring.gpg] https://packages.nginx.org/unit/debian/ bullseye unit' | tee --append /etc/apt/sources.list.d/unit.list \
    && apt-get update && apt-get install --assume-yes \
        systemd \
        unit=${NGINX_UNIT_VERSION} \
    # Redirect logs to stdout
    && ln --symbolic --force /dev/stdout /var/log/unit.log

COPY --from=vendor /usr/bin/composer /usr/local/bin/composer

# Install Postgresql client (https://www.postgresql.org/download/linux/debian/)
ARG POSTGRESQL_CLIENT_VERSION=15
# https://pecl.php.net/package/redis
ARG PHPREDIS_VERSION=6.0.2

RUN apt-get update \
    && apt-get install --assume-yes gpg \
    && curl https://www.postgresql.org/media/keys/ACCC4CF8.asc | gpg --dearmour --output /etc/apt/trusted.gpg.d/apt.postgresql.org.gpg >/dev/null \
    && echo "deb http://apt.postgresql.org/pub/repos/apt bullseye-pgdg main" | tee /etc/apt/sources.list.d/pgdg.list \
    && apt-get update && apt-get install --assume-yes \
        postgresql-client-${POSTGRESQL_CLIENT_VERSION} \
        libpcre3-dev \
        libpq-dev \
        zip \
        libzip-dev \
        libpng-dev \
        supervisor \
        cron \
    && pecl install \
        redis-${PHPREDIS_VERSION} \
    && docker-php-ext-install \
        pdo_pgsql \
        zip \
        gd \
        opcache \
    && docker-php-ext-configure \
        zip \
    && docker-php-ext-enable \
        redis

RUN mv "${PHP_INI_DIR}/php.ini-production" "${PHP_INI_DIR}/php.ini"
COPY ./environment/prod/app/php.ini "${PHP_INI_DIR}/conf.d/zzz-overrides-php.ini"
COPY ./environment/prod/app/supervisord.conf "/etc/supervisor/custom-supervisord.conf"
COPY ./environment/prod/app/entrypoint.sh /entrypoint.sh
COPY ./environment/prod/app/nginx-unit.config.json /var/lib/unit/conf.json

WORKDIR /application

# Copy Frontend build
COPY --chown=unit:unit --from=frontend /app_frontend_dependencies/public/build ./public/build/

# Copy Composer dependencies
COPY --chown=unit:unit --from=vendor /app_composer_dependencies/vendor ./vendor/

# Copy App files
COPY --chown=unit:unit . .

RUN composer dump-autoload --optimize

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]

ARG DEPLOYMENT_PROJECT_VERSION_ARG
ENV DEPLOY_VERSION=${DEPLOYMENT_PROJECT_VERSION_ARG}
