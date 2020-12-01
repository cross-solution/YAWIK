#!/bin/bash

# We need to install dependencies only for Docker
[[ ! -e /.dockerenv ]] && exit 0

set -xe

# Install git (the php image doesn't have it) which is required by composer
apt-get update -yqq
apt-get install git libpng-dev zlib1g-dev libicu-dev g++ -yqq

# Install phpunit, the tool that we will use for testing
curl -sS --location --output /usr/local/bin/phpunit https://phar.phpunit.de/phpunit.phar
chmod +x /usr/local/bin/phpunit

# Install composer
curl -sS https://getcomposer.org/installer > installer.php
php ./installer.php --install-dir=/usr/local/bin --filename=composer
chmod +x /usr/local/bin/composer

# Install mongodb, intl, gd
docker-php-ext-install intl gd

pecl install mongodb

php -i 

ls -l /usr/local/etc/php/


echo "##############################################"

echo "extension=mongodb.so" >> /usr/local/etc/php/php.ini-production
