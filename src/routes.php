<?php

use MttProjecteuler\Route\SolutionConverter;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app['converter.solution'] = function ($app) {
    return new SolutionConverter($app['pe_database.repository']);
};

$app->get('/', 'MttProjecteuler\\Controller\\WebController::index')
    ->bind('homepage');

$app->get('/about', 'MttProjecteuler\\Controller\\WebController::about')
    ->bind('about');

$app->get('/login', 'MttProjecteuler\\Controller\\SecurityController::login')
    ->bind('login');

$app->mount('/area_51', function (ControllerCollection $admin) {
    $admin->get('/', 'MttProjecteuler\\Controller\\AdminController::dashboard')
        ->bind('admin_dashboard');

    $admin->mount('/solutions', function (ControllerCollection $sol) {
        $sol->get('/', 'MttProjecteuler\\Controller\\Admin\\SolutionController::index')
            ->bind('admin_solutions_index');

        $sol->match('/create', 'MttProjecteuler\\Controller\\Admin\\SolutionController::create')
            ->method('GET|POST')
            ->bind('admin_solutions_new');

        $sol->match('/edit/{entity}', 'MttProjecteuler\\Controller\\Admin\\SolutionController::edit')
            ->method('GET|POST')
            ->assert('entity', '\d+')
            ->convert('entity', 'converter.solution:convert')
            ->bind('admin_solutions_edit');

        $sol->delete('/delete/{entity}', 'MttProjecteuler\\Controller\\Admin\\SolutionController::delete')
            ->assert('entity', '\d+')
            ->convert('entity', 'converter.solution:convert')
            ->bind('admin_solutions_delete');
    });

    $admin->mount('/languages', function (ControllerCollection $lang) {
        $lang->get('/', 'MttProjecteuler\\Controller\\Admin\\LangController::index')
            ->bind('admin_languages_index');
    });
});

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
