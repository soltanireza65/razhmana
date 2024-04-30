SHELL := /bin/bash

.PHONY: help up down

help:
	@grep -E '^[1-9a-zA-Z_-]+:.*?## .*$$|(^#--)' $(MAKEFILE_LIST) \
	| awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m %-43s\033[0m %s\n", $$1, $$2}' \
	| sed -e 's/\[32m #-- /[33m/'

build:
	docker compose build --no-cache

dev:
	docker compose -f "docker-compose.yml" up

stop:
	docker compose -f "docker-compose.yml" stop


build_web:
	docker build --pull -f "docker/nginx/Dockerfile" -t soltanireza65/razhmana-web .

build_php:
	docker build --pull -f "docker/php/Dockerfile" -t soltanireza65/razhmana-php .

	
tag_php:
	docker image tag razhmana-php soltanireza65/razhmana-php

tag_web:
	docker image tag razhmana-web soltanireza65/razhmana-web


push-php:
	docker image push soltanireza65/razhmana-php

push-web:
	docker image push soltanireza65/razhmana-web

tag:
	make tag-php && make tag-web

push:
	make push-php && make push-web

extract:
	docker cp repos-web-1:/app /home/reza/repos/temp
