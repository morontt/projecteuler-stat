<?php
/**
 * Created by PhpStorm.
 * User: morontt
 * Date: 05.06.16
 * Time: 11:39
 */

namespace MttProjecteuler\Controller\Admin;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class LangController
{
    /**
     * @param Application $app
     * @return string
     */
    public function index(Application $app)
    {
        return $app['twig']->render('admin/lang/index.html.twig', []);
    }
}
