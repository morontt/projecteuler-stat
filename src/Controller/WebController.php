<?php
/**
 * Created by PhpStorm.
 * User: morontt
 * Date: 31.05.16
 * Time: 23:57
 */

namespace MttProjecteuler\Controller;

use MttProjecteuler\Model\User;
use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class WebController extends BaseController
{
    /**
     * @param Application $app
     * @param string $page
     * @return string
     */
    public function index(Application $app, $page)
    {
        $page = (int)$page;
        $countPages = $app['pe_database.repository']->getCountResultsForStartpage();

        if ($page > $countPages) {
            throw new NotFoundHttpException(sprintf('WebController:index, page %d not found', $page));
        }

        $urlRenerator = $app['url_generator'];
        $paginationMeta = $this->getPaginationMetadata($page, $countPages, function ($p) use ($urlRenerator) {
            return $urlRenerator->generate('homepage', ['page' => $p], UrlGeneratorInterface::ABSOLUTE_PATH);
        });

        $results = $app['pe_database.repository']->getResultsForStartpage($page);

        return new Response($app['twig']->render('web/index.html.twig', compact('results', 'page', 'paginationMeta')));
    }

    /**
     * @param Application $app
     * @param User $user
     * @param string $page
     * @return string
     */
    public function user(Application $app, User $user, $page)
    {
        $page = (int)$page;
        $countPages = $app['pe_database.repository']->getCountResultsForUser($user);

        if ($page > $countPages) {
            throw new NotFoundHttpException(sprintf('WebController:user, page %d not found', $page));
        }

        $urlRenerator = $app['url_generator'];
        $paginationMeta = $this->getPaginationMetadata($page, $countPages, function ($p) use ($urlRenerator, $user) {
            return $urlRenerator->generate(
                'userpage', ['page' => $p, 'user' => $user->getSlug()],
                UrlGeneratorInterface::ABSOLUTE_PATH
            );
        });

        $results = $app['pe_database.repository']->getResultsForUser($user, $page);

        return new Response($app['twig']->render(
            'web/user.html.twig',
            compact('results', 'page', 'paginationMeta', 'user')
        ));
    }

    /**
     * @param Application $app
     * @return string
     */
    public function about(Application $app)
    {
        return new Response($app['twig']->render('web/about.html.twig', []));
    }
}
