<?php
/**
 * Created by PhpStorm.
 * User: morontt
 * Date: 05.06.16
 * Time: 2:20
 */

namespace MttProjecteuler\Controller;

use Silex\Application;

class AdminController extends BaseController
{
    /**
     * @param Application $app
     * @return mixed
     */
    public function dashboard(Application $app)
    {
        return $app['twig']->render('admin/dashboard.html.twig', []);
    }
}
