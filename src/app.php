<?php

use MttProjecteuler\Database\Migrator;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\HttpFragmentServiceProvider;

$app = new Application();
$app->register(new ServiceControllerServiceProvider());
$app->register(new TwigServiceProvider());
$app->register(new HttpFragmentServiceProvider());
$app['twig'] = $app->extend('twig', function ($twig, $app) {
    return $twig;
});

$parameters = require_once __DIR__ . '/../config/parameters.php';

$app->register(new DoctrineServiceProvider(), [
    'db.options' => [
        'driver' => 'pdo_mysql',
        'dbname' => $parameters['db_name'],
        'user' => $parameters['db_user'],
        'password' => $parameters['db_password'],
        'charset' => 'utf8',
    ],
]);

$app['pe_database.migrator'] = $app->factory(function ($app) {
    return new Migrator($app['db']);
});

return $app;
