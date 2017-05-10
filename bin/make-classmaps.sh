#!/bin/bash
#
# generates autoload_classmap.php in the module/<Module>/src directories
#

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
YAWIK_BASE=`dirname ${DIR}`
pushd ${YAWIK_BASE}

CLASSMAP_GENERATOR=${YAWIK_BASE}/vendor/bin/classmap_generator.php

modules=(Jobs Applications Install Core Organizations Cv Geo Auth)


for m in ${modules[@]}; do 
  cd ${YAWIK_BASE}/module/${m}/src
  ${CLASSMAP_GENERATOR} -s
done;

popd
