#!/bin/sh
set -e

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

# start selenium server
#./bin/start-selenium > /dev/null 2>&1 &
umask 0000
chmod 777 var/cache -Rf
chmod 777 var/log -Rf
exec docker-php-entrypoint "$@"