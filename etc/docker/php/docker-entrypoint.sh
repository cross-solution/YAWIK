#!/bin/sh
set -e

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

# start selenium server
exec ./bin/start-selenium > /dev/null 2>&1 &

exec docker-php-entrypoint "$@"
