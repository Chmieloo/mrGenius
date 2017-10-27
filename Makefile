SHELL=/bin/bash

all: composer-install

install: composer-install create-dirs

clean:
	rm bin/composer
	rm -rf vendor

composer:
	if [[ ! -f bin/composer || ! `php bin/composer --version | grep "version 1.5.2"` ]]; then curl -so bin/composer https://getcomposer.org/download/1.5.2/composer.phar; fi

composer-install: composer
	php bin/composer install

composer-install-prod: composer
	php bin/composer install --no-dev --optimize-autoloader --no-interaction --no-progress --no-ansi

create-dirs:
	sudo mkdir -p var/cache
	sudo chmod -R 777 var/cache
	sudo mkdir -p var/logs
	sudo chmod -R 777 var/logs
	sudo mkdir -p var/sessions
	sudo chmod -R 777 var/sessions

fix-permissions:
	HTTPDUSER=`ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`
	sudo setfacl -R -m u:"$$HTTPDUSER":rwX -m u:`whoami`:rwX var
	sudo setfacl -dR -m u:"$$HTTPDUSER":rwX -m u:`whoami`:rwX var