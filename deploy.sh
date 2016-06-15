#!/usr/bin/env bash

composer install --no-dev --optimize-autoloader

./console db:schema -e prod

rm -Rf var/cache/twig/*
