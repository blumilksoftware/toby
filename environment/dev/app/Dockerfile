# renovate: datasource=docker depName=php
ARG PHP_VERSION=8.3.10
ARG PHP_MODULE_NAME=php${PHP_VERSION}
# https://github.com/nginx/unit/tags
ARG UNIT_VERSION=1.31.1-1

FROM alpine:3.20.2@sha256:0a4eaa0eecf5f8c050e5bba433f58c052be7587ee8af3e8b3910ef9ab5fbe9f5 AS secops-tools

# https://github.com/FiloSottile/age/releases
ARG AGE_VERSION="1.1.1"

# https://github.com/getsops/sops/releases
ARG SOPS_VERSION="3.8.1"

RUN wget --output-document age.tar.gz "https://github.com/FiloSottile/age/releases/download/v${AGE_VERSION}/age-v${AGE_VERSION}-linux-amd64.tar.gz" \
    && tar --extract --file age.tar.gz \
    && mv age/age /usr/local/bin \
    && mv age/age-keygen /usr/local/bin \
    && wget --output-document /usr/local/bin/sops "https://github.com/getsops/sops/releases/download/v${SOPS_VERSION}/sops-v${SOPS_VERSION}.linux.amd64" \
    && chmod +x /usr/local/bin/sops

# https://hub.docker.com/r/composer/composer
FROM composer/composer:2.7.7-bin@sha256:aed26ab32c25912f683c971dd6d75999e5d235e14b84c6eb685fd97231c2a8a8 AS composer-bin

# https://hub.docker.com/_/node
FROM node:22.5.1-bullseye-slim@sha256:1381d1559eccfba1fd8ed9a0a47dddbb3602a185582783e1c049b8267530a5e4 AS node

FROM php:${PHP_VERSION}-cli-bullseye AS unit-modules-builder

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

FROM php:${PHP_VERSION}-cli-bullseye AS app

COPY --from=secops-tools /usr/local/bin/age /usr/local/bin/age
COPY --from=secops-tools /usr/local/bin/age-keygen /usr/local/bin/age-keygen
COPY --from=secops-tools /usr/local/bin/sops /usr/local/bin/sops

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

COPY --from=composer-bin ./composer /usr/bin/composer

ARG USER_NAME=host-user
ARG USER_ID=1000
ARG NGINX_UNIT_GROUP=unit

RUN adduser \
        --disabled-password \
        --uid ${USER_ID} \
        ${USER_NAME} \
    && usermod \
        --append \
        --groups \
        ${NGINX_UNIT_GROUP} \
        ${USER_NAME}

# Add node and npm
COPY --from=node --chown=${USER_NAME}:root /usr/local/lib/node_modules /usr/local/lib/node_modules
COPY --from=node --chown=${USER_NAME}:root /usr/local/bin/node /usr/local/bin/node
RUN ln --symbolic /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && chown --no-dereference ${USER_NAME}:root /usr/local/bin/npm \
    && ln --symbolic /usr/local/lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx \
    && chown --no-dereference ${USER_NAME}:root /usr/local/bin/npx

# Use the default development configuration
RUN mv "${PHP_INI_DIR}/php.ini-development" "${PHP_INI_DIR}/php.ini"

# https://pecl.php.net/package/redis
ARG PHPREDIS_VERSION=6.0.2

RUN apt-get update && apt-get install --assume-yes \
        libpcre3-dev \
        gnupg \
        zip \
        libzip-dev \
        libpng-dev \
        libpq-dev \
        supervisor \
    && pecl install \
        redis-${PHPREDIS_VERSION} \
    && docker-php-ext-install \
        pdo_pgsql \
        zip \
        gd \
    && docker-php-ext-enable \
        redis

# Install Postgresql client (https://www.postgresql.org/download/linux/debian/)
RUN curl https://www.postgresql.org/media/keys/ACCC4CF8.asc | gpg --dearmour --output /etc/apt/trusted.gpg.d/apt.postgresql.org.gpg >/dev/null \
    && bash -c 'echo "deb http://apt.postgresql.org/pub/repos/apt bullseye-pgdg main" > /etc/apt/sources.list.d/pgdg.list' \
    && apt-get update \
    && apt-get install --assume-yes postgresql-client-15

# For other versions check: https://pecl.php.net/package/xdebug
ARG XDEBUG_VERSION=3.3.1
ARG INSTALL_XDEBUG=true

RUN if [ ${INSTALL_XDEBUG} = true ]; then \
    pecl install xdebug-${XDEBUG_VERSION} \
    && docker-php-ext-enable xdebug \
;fi

COPY ./entrypoint.sh /entrypoint.sh

WORKDIR /application

ENTRYPOINT ["/entrypoint.sh"]

EXPOSE 80
