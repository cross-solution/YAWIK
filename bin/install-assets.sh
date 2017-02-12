#!/bin/bash

#
# will be executed by npm install as "prepublish" script
#

ASSETS_DIR=public/assets/

if [ ! -d "${ASSETS_DIR}" ]; then
	 mkdir -p ${ASSETS_DIR}
fi

cp -R node_modules/jquery/dist ${ASSETS_DIR}/jquery
cp -R node_modules/bootstrap3-dialog/dist ${ASSETS_DIR}/bootstrap3-dialog
cp -R node_modules/bootstrap-datepicker/dist ${ASSETS_DIR}/bootstrap-datepicker
cp -R node_modules/select2/dist ${ASSETS_DIR}/select2
cp -R node_modules/blueimp-file-upload ${ASSETS_DIR}/blueimp-file-upload
cp -R node_modules/pnotify/dist ${ASSETS_DIR}/pnotify
cp -R node_modules/bootstrap/dist ${ASSETS_DIR}/bootstrap

if [ ! -d "${ASSETS_DIR}/twitter-bootstrap-wizard" ]; then
         mkdir -p ${ASSETS_DIR}/twitter-bootstrap-wizard
fi
cp -R node_modules/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js ${ASSETS_DIR}/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js
cp -R node_modules/twitter-bootstrap-wizard/jquery.bootstrap.wizard.js ${ASSETS_DIR}/twitter-bootstrap-wizard/jquery.bootstrap.wizard.js