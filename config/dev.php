<?php

use Silex\Provider\MonologServiceProvider;
use Silex\Provider\WebProfilerServiceProvider;
use Sorien\Provider\PimpleDumpProvider;

// include the prod configuration
require __DIR__ . '/prod.php';

// enable the debug mode
$app['debug'] = true;

$app->register(new WebProfilerServiceProvider(), [
    'profiler.cache_dir' => __DIR__ . '/../var/cache/profiler',
]);
$app->register(new MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__.'/../var/logs/dev.log',
));
$app->register(new PimpleDumpProvider());
