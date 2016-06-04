<?php
/**
 * Created by PhpStorm.
 * User: morontt
 * Date: 05.06.16
 * Time: 11:34
 */

namespace MttProjecteuler\Controller\Admin;

use Carbon\Carbon;
use MttProjecteuler\Model\Solution;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class SolutionController
{
    /**
     * @param Application $app
     * @return string
     */
    public function index(Application $app)
    {
        $entities = $app['pe_database.repository']->findAllSolutions();

        return $app['twig']->render('admin/solution/index.html.twig', compact('entities'));
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

        $form = $app['form.factory']->create('MttProjecteuler\Form\SolutionType', $entity);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $app['db']->insert('solutions', $entity->toArray());

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
        $form = $app['form.factory']->create('MttProjecteuler\Form\SolutionType', $entity);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $entity->setUpdatedAt(new Carbon());
            $app['db']->update('solutions', $entity->toArray(), ['id' => $entity->getId()]);

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

        return $app->redirect($app['url_generator']->generate('admin_solutions_index'));
    }
}
