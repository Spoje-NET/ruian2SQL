all: composer phing
	

composer:
	composer update

phing:
	vendor/bin/phing

seed:
	./vendor/bin/phinx seed:run -s Places

