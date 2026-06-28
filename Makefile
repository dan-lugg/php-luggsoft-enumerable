.PHONY: test analyse cs cs-fix docs install

install:
	composer install

test:
	composer test

analyse:
	composer analyse

cs:
	composer cs

cs-fix:
	composer cs-fix

docs:
	composer docs
