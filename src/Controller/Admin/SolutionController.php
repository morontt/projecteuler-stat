<?php
/**
 * Created by PhpStorm.
 * User: morontt
 * Date: 05.06.16
 * Time: 11:34
 */

namespace MttProjecteuler\Controller\Admin;

use Carbon\Carbon;
use MttProjecteuler\Controller\BaseController;
use MttProjecteuler\Model\Solution;
use MttProjecteuler\Utils\Pygment;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SolutionController extends BaseController
{
    /**
     * @param Application $app
     * @return string
     */
    public function index(Application $app, $page)
    {
        $page = (int)$page;
        $entities = $app['pe_database.repository']->findAllSolutions($page);

        $countPages = $app['pe_database.repository']->getCountResults();
        if ($page > $countPages) {
            throw new NotFoundHttpException(sprintf('WebController:index, page %d not found', $page));
        }

        $urlRenerator = $app['url_generator'];
        $paginationMeta = $this->getPaginationMetadata($page, $countPages, function ($p) use ($urlRenerator) {
            return $urlRenerator->generate(
                'admin_solutions_index',
                ['page' => $p],
                UrlGeneratorInterface::ABSOLUTE_PATH
            );
        });

        return $app['twig']->render('admin/solution/index.html.twig', compact('entities', 'paginationMeta'));
    }

    /**
     * @param Application $app
     * @param Request $request
     * @return string|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function create(Application $app, Request $request)
    {
        $entity = new Solution();
        $entity->setCreatedBy($app['user']->getId());

        $form = $app['form.factory']->create(
            'MttProjecteuler\Form\SolutionType',
            $entity,
            [
                'repository' => $app['pe_database.repository'],
            ]
        );

        $form->handleRequest($request);
        if ($form->isValid()) {
            if ($form->get('generate')->getData() && $entity->getSourceLink()) {
                $lang = $app['pe_database.repository']->findLang((int)$entity->getLangId());
                if ($lang && $lang->getLexer()) {
                    $entity->setSourceHtml(Pygment::highlight($entity->getSourceLink(), $lang->getLexer()));
                }
            }

            $app['db']->insert('solutions', $entity->toArray());
            $app['session']->getFlashBag()->add('success', 'Решение создано');

            return $app->redirect($app['url_generator']->generate('admin_solutions_index'));
        }

        return $app['twig']
            ->render(
                'admin/solution/edit.html.twig',
                [
                    'entity' => $entity,
                    'form' => $form->createView(),
                ]
            );
    }

    /**
     * @param Application $app
     * @param Request $request
     * @param Solution $entity
     * @return string|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function edit(Application $app, Request $request, Solution $entity)
    {
        $form = $app['form.factory']->create(
            'MttProjecteuler\Form\SolutionType',
            $entity,
            [
                'repository' => $app['pe_database.repository'],
            ]
        );

        $form->handleRequest($request);
        if ($form->isValid()) {
            $entity->setUpdatedAt(new Carbon());

            if ($form->get('generate')->getData() && $entity->getSourceLink()) {
                $lang = $app['pe_database.repository']->findLang((int)$entity->getLangId());
                if ($lang && $lang->getLexer()) {
                    $entity->setSourceHtml(Pygment::highlight($entity->getSourceLink(), $lang->getLexer()));
                }
            }

            $app['db']->update('solutions', $entity->toArray(), ['id' => $entity->getId()]);
            $app['session']->getFlashBag()->add('success', sprintf('Решение ID: %s отредактировано', $entity->getId()));

            return $app->redirect($app['url_generator']->generate('admin_solutions_index'));
        }

        return $app['twig']
            ->render(
                'admin/solution/edit.html.twig',
                [
                    'entity' => $entity,
                    'form' => $form->createView(),
                ]
            );
    }

    /**
     * @param Application $app
     * @param Solution $entity
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Application $app, Solution $entity)
    {
        $app['db']->delete('solutions', ['id' => $entity->getId()]);
        $app['session']->getFlashBag()->add('success', sprintf('Решение ID: %s удалено', $entity->getId()));

        return $app->redirect($app['url_generator']->generate('admin_solutions_index'));
    }
}
