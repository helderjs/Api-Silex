<?php
/**
 * Created by PhpStorm.
 * User: helder
 * Date: 07/07/14
 * Time: 14:10
 */

namespace Api\Sms\Controller;


use Api\Sms\Entity\Category;
use Api\Sms\Entity\Config;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CategoryController
{
    public static function createRoutes(ControllerCollection $routing)
    {
        $routing->post('/category', [new self(), 'create']);

        $routing->get('/category/{id}', [new self(), 'info'])
            ->assert('id', '\d+');

        $routing->delete('/category/{id}', [new self(), 'delete'])
            ->assert('id', '\d+');
    }

    public function create(Application $app, Request $request)
    {
        if (!$request->get('gateway', false)) {
            return new JsonResponse(['success' => false, 'error' => 'Gateway id required.'], 500);
        }

        $gateway = $app['orm.em']->find('\Api\Sms\Entity\Gateway', $request->get('gateway'));

        if (!$gateway) {
            return new JsonResponse(['success' => false, 'error' => 'Gateway not found.'], 404);
        }

        $category = new Category();
        $category->setName($request->get('name'));
        $category->setUser($app['user']);

        $config = new Config();
        $config->setCategory($category);
        $config->setGateway($gateway);

        $app['orm.em']->persist($category);
        $app['orm.em']->flush();
        $app['orm.em']->persist($config);
        $app['orm.em']->flush();

        return new JsonResponse(
            [
                'id' => $category->getId(),
                'category' => $category->getName(),
                'config' => $config->getGateway()->getName()
            ]
        );
    }

    public function info(Application $app, Request $request)
    {
        if (!$request->get('id', false)) {
            return new JsonResponse(['success' => false, 'error' => 'Category id required.'], 500);
        }

        $category = $app['orm.em']->find('\Api\Sms\Entity\Category', $request->get('id'));

        if (!$category || $category->getUser()->getId() != $app['user']->getId()) {
            return new JsonResponse(['success' => true, 'error' => 'Category not found.'], 500);
        }

        $config = $app['orm.em']->getRepository('\Api\Sms\Entity\Config')->findOneBy(
            ['category' => $category, 'user' => $category->getUser()]
        );

        return new JsonResponse(
            [
                'id' => $category->getId(),
                'category' => $category->getName(),
                'config' => $config->getGateway()->getName()
            ]
        );
    }

    public function delete(Application $app, Request $request)
    {
        try {
            if (!$request->get('id', false)) {
                return new JsonResponse(['success' => false, 'error' => 'Category id required.'], 500);
            }

            $category = $app['orm.em']->find('\Api\Sms\Entity\category', $request->get('id'));

            if (!$category || $category->getUser()->getId() != $app['user']->getId()) {
                return new JsonResponse(['success' => false, 'error' => 'Category not found.'], 500);
            }

            /*$config = $app['orm.em']->getRepository('\Api\Sms\Entity\Config')->findOneBy(
                ['category' => $category, 'user' => $category->getUser()]
            );

            $app['orm.em']->remove($config);*/
            $app['orm.em']->remove($category);
            $app['orm.em']->flush();

            return new JsonResponse(['success' => true]);
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'error' => "Error while trying delete the category."], 500);
        }
    }
}