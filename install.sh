#!/bin/sh


PHING=phing.phar
COMPOSER=composer.phar

#
# assume, the script is running within a development environment, if ".git"
# directory exists
#
# Note: this leads to a problem, if you install YAWIK on eg. openshift. 
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
