<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/* @var \Silex\Application $app */

$app->get('/', 'MttProjecteuler\\Controller\\WebController::index')
    ->bind('homepage');

$app->get('/about', 'MttProjecteuler\\Controller\\WebController::about')
    ->bind('about');

$app->error(function (\Exception $e, Request $request, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    // 404.html, or 40x.html, or 4xx.html, or error.html
    $templates = array(
        'errors/' . $code . '.html.twig',
        'errors/' . substr($code, 0, 2) . 'x.html.twig',
        'errors/' . substr($code, 0, 1) . 'xx.html.twig',
        'errors/default.html.twig',
    );

    return new Response($app['twig']->resolveTemplate($templates)->render(['code' => $code]), $code);
});
