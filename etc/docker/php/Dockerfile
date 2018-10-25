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

# Add timezone
RUN rm /etc/localtime && \
    ln -s /usr/share/zoneinfo/UTC /etc/localtime && \
    "date"
RUN printf '[PHP]\ndate.timezone = "%s"\n', ${TIMEZONE} > /usr/local/etc/php/conf.d/tzone.ini

# Install composer
RUN curl -sS https://getcomposer.org/installer | \
    php -- --install-dir=/usr/local/bin --filename=composer

# Cleanup
RUN rm -rf /var/cache/apk/* \
    && find / -type f -iname \*.apk-new -delete \
    && rm -rf /var/cache/apk/*

RUN mkdir -p ${WORKDIR}

WORKDIR ${WORKDIR}

EXPOSE 9000
EXPOSE 9001

RUN echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "display_startup_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "display_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.remote_enable=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.remote_connect_back=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.remote_port=9000" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.remote_autostart=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

CMD ["php-fpm"]
