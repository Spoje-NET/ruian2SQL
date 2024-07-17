all: composer phing
	

composer:
	composer update

phing:
	vendor/bin/phing
