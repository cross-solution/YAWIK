#!/bin/sh


PHING=phing.phar
COMPOSER=composer.phar

#
# assume, the script is running within a development enviroment, if ".git"
# directory exists
#
if [ -d .git ] 
then
  DEVENV=1
fi;

#
# Download Phing
#
if [ ! -f $PHING ]
then
	echo "Download Phing"
	curl -sS http://www.phing.info/get/phing-latest.phar > $PHING
	chmod +x $PHING
fi;

# 
# Download Composer
#

if [ $DEVENV ]
then

	if [ ! -f $COMPOSER ]
	then
		echo "Download Composer"
		curl -sS https://getcomposer.org/installer | php
	fi;

	./$PHING
	
else
	./$PHING generate-autoload-config
fi;
