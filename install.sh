#!/bin/sh


PHING=phing.phar
COMPOSER=composer.phar


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

if [ ! -f $COMPOSER ]
then
	echo "Download Composer"
	curl -sS https://getcomposer.org/installer | php
fi;


./$PHING