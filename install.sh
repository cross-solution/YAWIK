#!/bin/bash

PHING=phing.phar
COMPOSER=composer.phar
PROPERTIES=build.properties

function usage {
  echo "";
  echo "-b|--build-properties       Location of the build.properties file";
  echo "-h|--help                   this usage";
  echo "";
  exit;  
}


while [ "$1" != "" ]; do
  case $1 in
   -b | --build-properties )
   	shift
   	PROPERTIES=$1
        ;;
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


if [ ! -f $PROPERTIES ];
then
  echo "no properties file found at $PROPERTIES. exit now."
  exit 1;
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

	./$PHING -Dbuild.properties $PROPERTIES
	
else
	./$PHING -Dbuild.properties $PROPERTIES generate-autoload-config
fi;


          