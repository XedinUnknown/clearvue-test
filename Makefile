.PHONY: all \
		build \
		install \
		install-php \
		qa \
		scan \
		test \
		test-php

include .env

all: build

build: install

install:
	$(MAKE) install-php
	$(MAKE) install-js

install-php: composer.lock
	composer install

install-js: yarn.lock
	yarn

qa:
	$(MAKE) test
	$(MAKE) scan
	wait

test:
	$(MAKE) test-php

test-php:
	vendor/bin/phpunit

scan:
	vendor/bin/psalm
	vendor/bin/phpcs -s
