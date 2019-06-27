#!/usr/bin/env bash

if [ -z ${GITHUB_TOKEN} ]; then
    # empty GITHUB_TOKEN we just exit
    exit 0;
fi;

YAWIK="${PWD}/bin/console subsplit"
BRANCH=${TRAVIS_BRANCH}

if [ ${BRANCH} != "master" ] && [ ${BRANCH} != "develop" ]; then
    # fallback to develop
    BRANCH="develop"
fi;

exec ${YAWIK} \
    --verbose \
    --ansi \
    --heads= ${BRANCH} \
    --source=https://${GITHUB_TOKEN}@github.com/cross-solution/YAWIK.git \
    --target=https://${GITHUB_TOKEN}@github.com/yawik