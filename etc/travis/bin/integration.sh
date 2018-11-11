#!/usr/bin/env bash

set -e
set -x

sleep 10

BUILD_DIR=./build/standard
# composer setup
composer create-project -sdev yawik/standard ${BUILD_DIR} --no-interaction
cd ${BUILD_DIR}

# tests setup
EXIT=0
mkdir -p build/behat
mkdir -p build/autoload
cp -Rvf ${TRAVIS_BUILD_DIR}/features features
cp -Rvf ${TRAVIS_BUILD_DIR}/config/autoload/*.* config/autoload/
cp -f ${TRAVIS_BUILD_DIR}/etc/travis/behat-integration.yml behat.yml
cp -f ${TRAVIS_BUILD_DIR}/etc/travis/env-integration.env .env
php -S localhost:8081 -t public/ > /dev/null 2>&1 &
sleep 5
./vendor/bin/behat || EXIT=1

./vendor/lakion/mink-debug-extension/travis/tools/upload-textfiles \"build/behat/*.log\" || echo "no behat logs"
./vendor/lakion/mink-debug-extension/travis/tools/upload-textfiles \"log/*.log\" || echo "no yawik logs"
./vendor/lakion/mink-debug-extension/travis/tools/upload-textfiles \"log/tracy/*.*\"  || echo "no tracy logs"
IMGUR_CLIENT_ID=bec050c54e1bb52 ${TRAVIS_BUILD_DIR}/bin/imgur-uploader build/behat/*.png  || echo "no behat images"

exit ${EXIT}