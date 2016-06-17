<?php

// configure your app for the production environment

use Doctrine\Common\Cache\FilesystemCache;

$app['twig.path'] = [__DIR__ . '/../templates'];
$app['twig.options'] = ['cache' => __DIR__ . '/../var/cache/twig'];

$app['pe_cache'] = function () {
    return new FilesystemCache(__DIR__ . '/../var/cache/content');
};
