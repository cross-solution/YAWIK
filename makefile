
all:
	@echo "Makefile for Cross Applicant Management."
	@echo
	@echo "Available Sections:"
	@echo
	@echo "    composer-install         Install composer dependencies"
	@echo "    install-config           Copy *.global configuration files from modules to /config/autoload/"
	@echo "    create-directories       Create directories needed for logs, docs etc. and set permissions."
	@echo "    install                  Runs composer-install, install-config and create-directories."
	@echo "    remove-config            Deletes all *.global.php config files from /config/autoload."
	@echo "    remove-directories       Deleted directories created with create-directories and all content."
	@echo "    composer-uninstall       Deleted the /vender directory and all contents."
	@echo "    uninstall                Runs remove-directories, composer-uninstall and remove-config."
	@echo 
	
	
install: composer-install create-directories install-config

composer-install:
	@echo "=="; echo "== Install composer dependencies"; echo "==" 
	curl -sS https://getcomposer.org/installer | php
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
	@echo "+ create directories logs, docs and coverage:"
	mkdir -p ./log
	mkdir -p ./public/docs
	mkdir -p ./public/coverage
	
uninstall: remove-directories composer-uninstall remove-config
	
remove-config:
	@echo "=="; echo "== remove configuration files"; echo "=="
	@rm -v ./config/autoload/*.global.php
	@[ "$$(ls -A ./config/autoload)" ] || rmdir -v ./config/autoload 
	@echo	
	
remove-directories:
	@echo "+ remove-log-dir:"
	-rm -rf ./log
	-rm -rf ./public/docs

composer-uninstall:
	@echo "=="; echo "== Remove composer dependecies"; echo "=="
	-rm -rf ./vendor
	@echo
	
doc: create-directories
	@echo "=="; echo "== generate docs"; echo "=="
	phpdoc -c phpdoc.xml
	
test: create-directories
	@echo "=="; echo "== dun tests"; echo "=="
	cd module/Core/test && phpunit -c phpunit-coverage.xml && cd ../../..
	