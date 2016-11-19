<?php
/**
 * Created by PhpStorm.
 * User: morontt
 * Date: 05.06.16
 * Time: 1:39
 */

namespace MttProjecteuler\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class SecurityController
{
    /**
     * @param Application $app
     * @param Request $request
     *
     * @return string
     */
    public function login(Application $app, Request $request)
    {
        $token = $app['csrf.token_manager']->refreshToken('authenticate');

        return $app['twig']->render('security/login.html.twig', [
            'error' => $app['security.last_error']($request),
            'last_username' => $app['session']->get('_security.last_username'),
            'csrf_token' => $token,
        ]);
    }
}
