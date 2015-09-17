#!/bin/bash

PHING=phing.phar
COMPOSER=composer.phar
PROPERTIES=build.properties

function usage {
  echo "";
  echo "-h|--help                   this usage";
  echo "";
  exit;  
}


while [ "$1" != "" ]; do
  case $1 in
   -h | --help )
   	usage
        exit
        ;;
   esac
   shift
done


#
# assume, the script is running within a development environment, if ".git"
# directory exists
#
# Note: this leads to a problem, if you install YAWIK on eg. openshift. 
#
if [ -d .git ] || [ "$1"=="devenv" ]; 
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


echo "Installing úsing: $PROPERTIES"

if [ $DEVENV ]
then

	if [ ! -f $COMPOSER ]
	then
		echo "Download Composer"
		curl -sS https://getcomposer.org/installer | php
	fi;

	./$COMPOSER install
fi;


          