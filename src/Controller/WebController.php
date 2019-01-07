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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class WebController extends BaseController
{
    /**
     * @param Application $app
     * @param Request $request
     * @param string $page
     *
     * @return Response
     */
    public function index(Application $app, Request $request, $page)
    {
        $page = (int)$page;

        $etag = $this->computeEtag($app, 'index', $app['pe_database.repository']->lastModifiedStartpage(), $page);
        $response = new Response();
        $response->setEtag($etag);
        if ($response->isNotModified($request)) {
            return $response;
        }

        /* @var \Doctrine\Common\Cache\Cache $cache */
        $cache = $app['pe_cache'];
        if ($cache->contains($etag)) {
            $content = $cache->fetch($etag);
        } else {
            $countPages = $app['pe_database.repository']->getCountResultsForStartpage();

            if ($page > $countPages) {
                throw new NotFoundHttpException(sprintf('WebController:index, page %d not found', $page));
            }

            $urlRenerator = $app['url_generator'];
            $paginationMeta = $this->getPaginationMetadata($page, $countPages, function ($p) use ($urlRenerator) {
                return $urlRenerator->generate('homepage', ['page' => $p], UrlGeneratorInterface::ABSOLUTE_PATH);
            });

            $results = $app['pe_database.repository']->getResultsForStartpage($page);
            $content = $app['twig']->render('web/index.html.twig', compact('results', 'page', 'paginationMeta'));
            $cache->save($etag, $content);
        }

        $response = new Response($content);
        $response->setEtag($etag);

        return $response;
    }

    /**
     * @param Application $app
     * @param Request $request
     * @param User $user
     * @param string $page
     *
     * @return Response
     */
    public function user(Application $app, Request $request, User $user, $page)
    {
        $page = (int)$page;

        $etag = $this->computeEtag($app, 'user', $app['pe_database.repository']->lastModifiedByUser($user), $page);
        $response = new Response();
        $response->setEtag($etag);
        if ($response->isNotModified($request)) {
            return $response;
        }

        /* @var \Doctrine\Common\Cache\Cache $cache */
        $cache = $app['pe_cache'];
        if ($cache->contains($etag)) {
            $content = $cache->fetch($etag);
        } else {
            $countPages = $app['pe_database.repository']->getCountResultsForUser($user);

            if ($page > $countPages) {
                throw new NotFoundHttpException(sprintf('WebController:user, page %d not found', $page));
            }

            $urlRenerator = $app['url_generator'];
            $paginationMeta = $this->getPaginationMetadata(
                $page,
                $countPages,
                function ($p) use ($urlRenerator, $user) {
                    return $urlRenerator->generate(
                        'userpage', ['page' => $p, 'user' => $user->getSlug()],
                        UrlGeneratorInterface::ABSOLUTE_PATH
                    );
                }
            );

            $langStat = $app['pe_database.repository']->getLangStatisticsByUser($user);
            if (count($langStat) % 2 == 1) {
                $langStat[] = ['cnt' => '', 'name' => '...'];
            }

            $results = $app['pe_database.repository']->getResultsForUser($user, $page);
            $content = $app['twig']->render(
                'web/user.html.twig',
                compact('results', 'page', 'paginationMeta', 'user', 'langStat')
            );
            $cache->save($etag, $content);
        }

        $response = new Response($content);
        $response->setEtag($etag);

        return $response;
    }

    /**
     * @param Application $app
     * @param Request $request
     * @param string $number
     *
     * @return Response
     */
    public function problem(Application $app, Request $request, $number)
    {
        $number = (int)$number;

        $etag = $this->computeEtag(
            $app,
            'problem',
            $app['pe_database.repository']->lastModifiedByProblem($number),
            $number
        );

        $response = new Response();
        $response->setEtag($etag);
        if ($response->isNotModified($request)) {
            return $response;
        }

        /* @var \Doctrine\Common\Cache\Cache $cache */
        $cache = $app['pe_cache'];
        if ($cache->contains($etag)) {
            $content = $cache->fetch($etag);
        } else {
            $results = $app['pe_database.repository']->getResultsByProblem($number);
            $problem = $app['pe_database.repository']->findProblem($number);

            $content = $app['twig']->render('web/problem.html.twig', compact('results', 'number', 'problem'));
            $cache->save($etag, $content);
        }

        $response = new Response($content);
        $response->setEtag($etag);

        return $response;
    }

    /**
     * @param Application $app
     * @param Request $request
     * @param string $id
     *
     * @return Response
     */
    public function solution(Application $app, Request $request, $id)
    {
        $id = (int)$id;
        $result = $app['pe_database.repository']->getSingleResult($id);

        if ($result === false) {
            throw new NotFoundHttpException(sprintf('WebController:solution, solution %d not found', $id));
        }

        $etag = $this->computeEtag($app, 'solution', $result['updated']);
        $response = new Response();
        $response->setEtag($etag);
        if ($response->isNotModified($request)) {
            return $response;
        }

        $problem = $app['pe_database.repository']->findProblem($result['problem_number']);

        $response = new Response($app['twig']->render('web/solution.html.twig', compact('result', 'id', 'problem')));
        $response->setEtag($etag);

        return $response;
    }

    /**
     * @param Application $app
     * @param Request $request
     *
     * @return Response
     */
    public function about(Application $app, Request $request)
    {
        $etag = $this->computeEtagStatic($app, 'about');
        $response = new Response();
        $response->setEtag($etag);
        if ($response->isNotModified($request)) {
            return $response;
        }

        $response = new Response($app['twig']->render('web/about.html.twig', []));
        $response->setEtag($etag);

        return $response;
    }
}
