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
     * @return Response
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
     * @return Response
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
     * @param $number
     * @return Response
     */
    public function problem(Application $app, $number)
    {
        $number = (int)$number;

        $results = $app['pe_database.repository']->getResultsByProblem($number);
        $problem = $app['pe_database.repository']->findProblem($number);

        if (!$problem) {
            $curl = curl_init('https://projecteuler.net/problem=' . $number);
            curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:47.0) Gecko/20100101 Firefox/47.0');
            curl_setopt($curl, CURLOPT_TIMEOUT, 5);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $data = curl_exec($curl);

            if (curl_errno($curl)) {
                $problem = ['title' => 'undefined'];
            } else {
                curl_close($curl);

                $matches = [];
                if (preg_match('/<h2>([^<]+)<\/h2>/', $data, $matches)) {
                    $problem = [
                        'title' => $matches[1],
                        'problem_number' => $number,
                    ];
                    $app['db']->insert('problems', $problem);
                } else {
                    $problem = ['title' => 'undefined'];
                }
            }
        }

        return new Response($app['twig']->render('web/problem.html.twig', compact('results', 'number', 'problem')));
    }

    /**
     * @param Application $app
     * @return Response
     */
    public function about(Application $app)
    {
        return new Response($app['twig']->render('web/about.html.twig', []));
    }
}
