FROM php:7.2-fpm-alpine

MAINTAINER Anthonius <me@itstoni.com>

ENV UMASK 0

ENV WORKDIR "/var/www/yawik"
ARG TIMEZONE

RUN apk upgrade --update && apk --no-cache add \
    gcc g++ make git autoconf tzdata openntpd libcurl curl-dev coreutils \
    libmcrypt-dev freetype-dev libxpm-dev libjpeg-turbo-dev libvpx-dev \
    libpng-dev libxml2-dev postgresql-dev icu-dev pcre-dev

RUN apk add --no-cache bash

RUN docker-php-ext-configure intl \
    && docker-php-ext-configure opcache \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ \
    --with-jpeg-dir=/usr/include/ --with-png-dir=/usr/include/ \
    --with-xpm-dir=/usr/include/

RUN docker-php-ext-install -j$(nproc) gd iconv pdo pdo_pgsql pdo_mysql curl \
    mbstring json xml xmlrpc zip bcmath intl opcache

RUN pecl channel-update pecl.php.net

# Install xDebug and Redis
RUN docker-php-source extract \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb

RUN pecl install xdebug && docker-php-ext-enable xdebug

RUN docker-php-source delete

RUN printf '[PHP]\ndate.timezone = "%s"\n', ${TIMEZONE} > /usr/local/etc/php/conf.d/tzone.ini

RUN apk add openjdk8-jre

# Cleanup
RUN rm -rf /var/cache/apk/* \
    && find / -type f -iname \*.apk-new -delete \
    && rm -rf /var/cache/apk/*

RUN mkdir -p ${WORKDIR}

WORKDIR ${WORKDIR}

EXPOSE 9000
EXPOSE 9001

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint
ENTRYPOINT ["docker-entrypoint"]
RUN chmod +x /usr/local/bin/docker-entrypoint

CMD ["php-fpm"]
