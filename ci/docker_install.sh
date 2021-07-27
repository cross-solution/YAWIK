#!/bin/bash

# We need to install dependencies only for Docker
[[ ! -e /.dockerenv ]] && exit 0

set -xe

# Install git (the php image doesn't have it) which is required by composer
apt-get update -yqq
apt-get install git \
	unzip libpng-dev zlib1g-dev libicu-dev g++ -yqq

# install nodejs
curl -sL https://deb.nodesource.com/setup_12.x | bash -
apt-get install -y nodejs

# Install phpunit, the tool that we will use for testing
curl -sS --location --output /usr/local/bin/phpunit https://phar.phpunit.de/phpunit.phar
chmod +x /usr/local/bin/phpunit

# Install composer
curl -sS https://getcomposer.org/installer > installer.php
php ./installer.php --install-dir=/usr/local/bin --filename=composer
chmod +x /usr/local/bin/composer

# Install mongodb, intl, gd
docker-php-ext-install intl gd

# install mongo extension
pecl install mongodb

# activate mongo extension
echo "extension=mongodb.so" > /usr/local/etc/php/conf.d/docker-php-ext-mongodb.ini


