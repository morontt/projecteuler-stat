#!/usr/bin/env bash

composer install --no-dev --optimize-autoloader

./console db:schema

rm -Rf var/cache/twig/*
