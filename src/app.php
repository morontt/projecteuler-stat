<?php

use MttProjecteuler\Database\Migrator;
use MttProjecteuler\Database\Repository;
use MttProjecteuler\Database\UserProvider;
use Silex\Application;
use Silex\Provider\CsrfServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\LocaleServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\HttpFragmentServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

$app = new Application();
$app->register(new ServiceControllerServiceProvider());
$app->register(new TwigServiceProvider());
$app->register(new HttpFragmentServiceProvider());
$app['twig'] = $app->extend('twig', function ($twig, $app) {
    $twig->addFilter('lambda_filter', new \Twig_SimpleFilter('call', function () {
        $arguments = func_get_args();
        $callable = array_shift($arguments);

        if (!is_callable($callable)) {
            throw new InvalidArgumentException();
        }

        return call_user_func_array($callable, $arguments);
    }));

    return $twig;
});

$parameters = require_once __DIR__ . '/../config/parameters.php';

$app->register(new DoctrineServiceProvider(), [
    'db.options' => [
        'driver' => 'pdo_mysql',
        'host' => $parameters['db_host'],
        'dbname' => $parameters['db_name'],
        'user' => $parameters['db_user'],
        'password' => $parameters['db_password'],
        'charset' => 'utf8',
    ],
]);

$app['pe_database.migrator'] = $app->factory(function ($app) {
    return new Migrator($app['db']);
});

$app['pe_database.repository'] = function ($app) {
    return new Repository($app['db']);
};

$app->register(new SessionServiceProvider());
$app->register(new CsrfServiceProvider());
$app->register(new SecurityServiceProvider(), [
    'security.firewalls' => [
        'login' => [
            'pattern' => '^/login$',
        ],
        'secured' => [
            'pattern' => '^.*$',
            'anonymous' => true,
            'form' => [
                'login_path' => '/login',
                'check_path' => '/login_check',
                'with_csrf' => true,
            ],
            'logout' => [
                'logout_path' => '/logout',
                'invalidate_session' => true,
            ],
            'users' => function () use ($app) {
                return new UserProvider($app['pe_database.repository']);
            },
        ],
    ],
]);

$app['security.default_encoder'] = function () {
    return new MessageDigestPasswordEncoder('gost', false, 4321);
};

$app['security.access_rules'] = [
    ['^/area_51', 'ROLE_USER'],
];

$app->register(new FormServiceProvider());
$app->register(new ValidatorServiceProvider());
$app->register(new LocaleServiceProvider());
$app->register(new TranslationServiceProvider(), [
    'locale_fallbacks' => ['ru'],
]);

return $app;
