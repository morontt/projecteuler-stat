#!/usr/bin/env bash

LAST_HASH=`git log --pretty=format:"%h" -n 1`
sed -i "s/'git_hash' => '[[:alnum:]]\{7\}'/'git_hash' => '"$LAST_HASH"'/" ./config/parameters.php

composer install --no-dev --optimize-autoloader

./console db:schema -e prod

rm -Rf var/cache/twig/*
rm -Rf var/cache/content/*
