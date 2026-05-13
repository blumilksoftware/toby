# renovate: datasource=docker depName=php
ARG PHP_VERSION=8.4.11
ARG PHP_MODULE_NAME=php${PHP_VERSION}
# https://github.com/nginx/unit/tags
ARG UNIT_VERSION=1.34.1-1

FROM alpine:3.22.4@sha256:310c62b5e7ca5b08167e4384c68db0fd2905dd9c7493756d356e893909057601 AS secops-tools

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
FROM composer/composer:2.9.7-bin@sha256:a742abd4ab1087c2d98bc63e84fe6a510886c5afbe35ef227d5932a22ee797dc AS composer-bin

# https://hub.docker.com/_/node
FROM node:22.22.2-bullseye-slim@sha256:f414ad225653500910a84d4206bb3f10f449acfb01815e3b41a0774f450ee802 AS node

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
# renovate: datasource=github-tags depName=phpredis/phpredis
ARG PHPREDIS_VERSION=6.3.0

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
# renovate: datasource=github-tags depName=xdebug/xdebug
ARG XDEBUG_VERSION=3.5.1
ARG INSTALL_XDEBUG=true

RUN if [ ${INSTALL_XDEBUG} = true ]; then \
    pecl install xdebug-${XDEBUG_VERSION} \
    && docker-php-ext-enable xdebug \
;fi

COPY ./entrypoint.sh /entrypoint.sh

WORKDIR /application

ENTRYPOINT ["/entrypoint.sh"]

EXPOSE 80
