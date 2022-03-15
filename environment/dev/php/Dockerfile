FROM ghcr.io/blumilksoftware/php:8.1

ARG XDEBUG_VERSION=3.1.2
ARG INSTALL_XDEBUG=false

RUN if [ ${INSTALL_XDEBUG} = true ]; then \
    apk --no-cache add $PHPIZE_DEPS \
    && pecl install xdebug-${XDEBUG_VERSION} \
    && docker-php-ext-enable xdebug \
;fi

RUN apk add --no-cache pcre-dev $PHPIZE_DEPS \
    postgresql-dev \
    zip \
    libzip-dev \
    libpng-dev \
    && pecl install redis \
    && docker-php-ext-install \
    pdo_pgsql \
    zip \
    gd \
    && docker-php-ext-configure \
    zip \
    && docker-php-ext-enable \
    redis
