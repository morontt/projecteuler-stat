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
        return $app['twig']->render('web/index.html.twig', []);
    }

    /**
     * @param Application $app
     * @return string
     */
    public function about(Application $app)
    {
        return $app['twig']->render('web/about.html.twig', []);
    }
}
