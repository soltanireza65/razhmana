#!/bin/bash

docker compose down

docker image rm soltanireza65/razhmana-php:latest
docker image rm soltanireza65/razhmana-web:latest

docker image pull soltanireza65/razhmana-php:latest
docker image pull soltanireza65/razhmana-web:latest

docker compose up --build