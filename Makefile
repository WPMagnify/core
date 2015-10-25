.PHONY: test


test:
	./bin/install-wp-tests.sh magnifycore_test root ''
	php vendor/bin/phpunit
