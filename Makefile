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


tag_php:
	docker tag soltanireza65/razhmana-php:latest

tag_web:
	docker tag soltanireza65/razhmana-web:latest


push-php:
	docker image push soltanireza65/razhmana-php:latest

push-web:
	docker image push soltanireza65/razhmana-web:latest

push:
	make push-php && make push-web

extract:
	docker cp repos-web-1:/app /home/reza/repos/temp




docker build -t baeldung-java:5 .
docker tag baeldung-java:6 baeldung-java:8

docker build --pull -f "docker/nginx/Dockerfile" -t soltanireza65/razhmana-web:latest .
docker build --pull -f "docker/php/Dockerfile" -t soltanireza65/razhmana-php:latest .