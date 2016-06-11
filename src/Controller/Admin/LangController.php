<?php
/**
 * Created by PhpStorm.
 * User: morontt
 * Date: 05.06.16
 * Time: 11:39
 */

namespace MttProjecteuler\Controller\Admin;

use Carbon\Carbon;
use MttProjecteuler\Controller\BaseController;
use MttProjecteuler\Model\Lang;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class LangController extends BaseController
{
    /**
     * @param Application $app
     * @return string
     */
    public function index(Application $app)
    {
        $entities = $app['pe_database.repository']->findAllLanguages();

        return $app['twig']->render('admin/lang/index.html.twig', compact('entities'));
    }

    /**
     * @param Application $app
     * @param Request $request
     * @return string|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function create(Application $app, Request $request)
    {
        $entity = new Lang();
        $entity->setCreatedBy($app['user']->getId());

        $form = $app['form.factory']->create('MttProjecteuler\Form\LangType', $entity);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $app['db']->insert('languages', $entity->toArray());
            $app['session']->getFlashBag()->add('success', sprintf('Язык %s создан', $entity));

            return $app->redirect($app['url_generator']->generate('admin_languages_index'));
        }

        return $app['twig']
            ->render(
                'admin/lang/edit.html.twig',
                [
                    'entity' => $entity,
                    'form' => $form->createView(),
                ]
            );
    }

    /**
     * @param Application $app
     * @param Request $request
     * @param Lang $entity
     * @return string|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function edit(Application $app, Request $request, Lang $entity)
    {
        $form = $app['form.factory']->create('MttProjecteuler\Form\LangType', $entity);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $entity->setUpdatedAt(new Carbon());
            $app['db']->update('languages', $entity->toArray(), ['id' => $entity->getId()]);
            $app['session']->getFlashBag()->add('success', sprintf('Язык %s отредактирован', $entity));

            return $app->redirect($app['url_generator']->generate('admin_languages_index'));
        }

        return $app['twig']
            ->render(
                'admin/lang/edit.html.twig',
                [
                    'entity' => $entity,
                    'form' => $form->createView(),
                ]
            );
    }

    /**
     * @param Application $app
     * @param Lang $entity
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Application $app, Lang $entity)
    {
        $app['db']->delete('languages', ['id' => $entity->getId()]);
        $app['session']->getFlashBag()->add('success', sprintf('Язык %s удалён', $entity));

        return $app->redirect($app['url_generator']->generate('admin_languages_index'));
    }
}
