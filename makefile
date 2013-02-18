
all:
	@echo "Usage: make install     : Create directories and set permissions, run composer install"
	@echo "       make uninstall   : Remove directories and all contents (inkl. \"vendor\" directory from composer"
	
install: composer-install create-directories

composer-install:
	php composer.phar self-update
	php composer.phar install
	
create-directories:
#	@echo "+ create-cache-dir:"
#	mkdir ./cache
#	mkdir ./cache/osm 
#	mkdir -p ./cache/import-u7/done
#	mkdir ./cache/clicktracker
#	mkdir ./cache/database
#	chmod -R a+w ./cache
	
	
uninstall: remove-directories composer-uninstall
	
remove-directories:
#	@echo "+ remove-cache-dir:"
#	-rm -rf ./cache

composer-uninstall:
	-rm -f  ./composer.lock 
	-rm -rf ./vendor
	