<?php
/**
 * Created by PhpStorm.
 * User: morontt
 * Date: 31.05.16
 * Time: 23:57
 */

namespace MttProjecteuler\Controller;

use Silex\Application;

class WebController
{
    /**
     * @param Application $app
     * @param string $page
     * @return string
     */
    public function index(Application $app, $page)
    {
        $results = $app['pe_database.repository']->getResultsForStartpage((int)$page);

        return $app['twig']->render('web/index.html.twig', compact('results'));
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
