
all:
	@echo "Usage: make install     : Create directories and set permissions, run composer install"
	@echo "       make uninstall   : Remove directories and all contents (inkl. \"vendor\" directory from composer"
	
install: composer-install create-directories install-config

composer-install:
	@echo "=="; echo "== Install composer dependencies"; echo "==" 
	php composer.phar self-update
	php composer.phar install
	@echo
	
install-config:
	@echo "=="; echo "== Copy configuration files"; echo "=="
	mkdir -p ./config/autoload
	@for FILE in $$(find . -wholename "*/config/*.dist"); do \
		DEST=$${FILE##*/}; DEST="./config/autoload/$${DEST%.dist}"; \
		cp -v $$FILE $$DEST; \
	done
	@echo
	
create-directories:
#	@echo "+ create-cache-dir:"
#	mkdir ./cache
#	mkdir ./cache/osm 
#	mkdir -p ./cache/import-u7/done
#	mkdir ./cache/clicktracker
#	mkdir ./cache/database
#	chmod -R a+w ./cache
	
	
uninstall: remove-directories composer-uninstall remove-config
	
remove-config:
	@echo "=="; echo "== remove configuration files"; echo "=="
	@rm -v ./config/autoload/*.global.php
	@[ "$$(ls -A ./config/autoload)" ] || rmdir -v ./config/autoload 
	@echo	
	
remove-directories:
#	@echo "+ remove-cache-dir:"
#	-rm -rf ./cache

composer-uninstall:
	@echo "=="; echo "== Remove composer dependecies"; echo "=="
	-rm -rf ./vendor
	@echo
	