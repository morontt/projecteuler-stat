<?php
/**
 * Created by PhpStorm.
 * User: morontt
 * Date: 31.05.16
 * Time: 23:57
 */

namespace MttProjecteuler\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class WebController
{
    /**
     * @param Application $app
     * @return string
     */
    public function index(Application $app)
    {
        return $app['twig']->render('index.html.twig', []);
    }

    /**
     * @param Application $app
     * @return string
     */
    public function about(Application $app)
    {
        return $app['twig']->render('about.html.twig', []);
    }

    /**
     * @param Application $app
     * @param Request $request
     * @return string
     */
    public function login(Application $app, Request $request)
    {
        $token = $app['csrf.token_manager']->refreshToken('authenticate');

        return $app['twig']->render('login.html.twig', [
            'error' => $app['security.last_error']($request),
            'last_username' => $app['session']->get('_security.last_username'),
            'csrf_token' => $token,
        ]);
    }
}
