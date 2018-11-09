#!/bin/sh
set -e

echo $PWD
PHP_INI_OVERRIDE="etc/docker/php/php-ini-overrides.ini"
if [ ! -f ${PHP_INI_OVERRIDE} ]; then
    # just using default configuration
    PHP_INI_OVERRIDE="etc/docker/php/php-ini-overrides.ini.dist";
fi

TARGET=/usr/local/etc/php/conf.d/99-overrides.ini
cp -v ${PHP_INI_OVERRIDE} ${TARGET}

export DOCKER_ENV="yes"

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

./bin/start-selenium > /dev/null 2>&1 &
./bin/console clear-cache
umask 0000
chmod 777 var/cache -Rf
chmod 777 var/log -Rf
exec docker-php-entrypoint "$@"