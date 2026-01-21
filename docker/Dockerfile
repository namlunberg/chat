# ************************************* #
FROM php:8.4-fpm-alpine AS php_build

ARG XDEBUG

RUN apk --no-cache update

RUN apk --no-cache upgrade

RUN apk add --no-cache $PHPIZE_DEPS tzdata acl yarn file gettext icu-dev libzip-dev freetype-dev libjpeg-turbo-dev libpng-dev pcre-dev

RUN docker-php-ext-configure gd --with-freetype --with-jpeg

RUN docker-php-ext-configure intl

RUN apk add --update --no-cache --virtual .build-dependencies $PHPIZE_DEPS \
        && pecl install apcu \
        && docker-php-ext-enable apcu \
        && pecl clear-cache \
        && apk del .build-dependencies

RUN apk add postgresql-dev
RUN docker-php-ext-install -j$(nproc) intl pdo pdo_pgsql zip gd opcache

RUN apk add --update linux-headers

RUN if [ "$XDEBUG" = "1" ]; then pecl install xdebug && rm -rf /tmp/pear && docker-php-ext-enable xdebug; fi

ENV TZ=Europe/Moscow
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN apk del $PHPIZE_DEPS

##COPY docker/php/php.ini /usr/local/etc/php/php.ini
##COPY docker/php/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

COPY --from=composer /usr/bin/composer /usr/local/bin/composer
RUN composer self-update -vvv && chmod 777 /tmp

RUN apk add git
RUN apk add mc

##RUN apk add php84-pdo_mysql

WORKDIR /app
# ************************************* #
FROM php_build AS php_fpm
