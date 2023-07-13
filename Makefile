current-dir := $(dir $(abspath $(lastword $(MAKEFILE_LIST))))

IMAGE=php-price-engine

.PHONY: build
build: deps
	docker build -t $(IMAGE) .

.PHONY: clean
clean:
	docker rmi $(IMAGE)

.PHONY: deps
deps: composer-install

.PHONY: composer-install
composer-install: CMD=install

.PHONY: composer-update
composer-update: CMD=update

.PHONY: composer-require
composer-require: CMD=require
composer-require: INTERACTIVE=-ti --interactive

.PHONY: composer
composer composer-install composer-update composer-require composer-require-module:
	@docker run --rm $(INTERACTIVE) --volume $(current-dir):/app --user $(id -u):$(id -g) \
		composer:2.3.7 $(CMD) \
			--ignore-platform-reqs \
			--no-ansi

.PHONY: test
test: composer-install
	docker run --rm -v $(PWD):/app -w /app $(IMAGE) vendor/bin/phpunit $(FILTER_TEST_OPTIONS);