<?php

namespace Api\Sms\Controller;

use Api\Sms\Entity\Sms;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class Message
 * Controller para as seguintes ações
 *  # send
 *    > POST - Envia uma mensagem sms
 *
 *  # response
 *    > POST - Busca mensagens de resposta a um sms
 *
 *  # info
 *    > POST - Retorna informações de uma mesagem sms
 *
 * @package Api\Sms\Controller
 */
class MessageController
{
    public static function createRoutes(ControllerCollection $routing)
    {
        $routing->post('/', [new self(), 'send']);

        $routing->get('/{id}', [new self(), 'info'])
            ->assert('id', '\d+');

        $routing->get('/{id}/response', [new self(), 'response'])
            ->assert('id', '\d+');
    }

    public function send(Application $app, Request $request)
    {
        $user = $app['user'];
        $sendNow = true;

        $sms = new Sms();
        $sms->setDdd($request->get('ddd'));
        $sms->setNumber($request->get('number'));
        $sms->setType(Sms::SMS_TYPE_OUT);
        $sms->setStatus(Sms::SMS_STATUS_CREATED);
        $sms->setMessage($request->get('message'));
        $sms->setSignature($request->get('signature', $user->getName()));

        if ($request->get('schedule', false)) {
            $dateTime = new \DateTime($request->get('schedule'));
            $sms->setSchedule($dateTime);

            $today = new \DateTime('now');
            $sendNow = $today >= $dateTime ? true : false;
        }

        if ($request->get('category', false)) {
            $category = $category = $app['orm.em']->find('Api\Sms\Entity\Category', $request->get('category'));

            if (!$category) {
                return new JsonResponse(
                    [
                        'error' => true,
                        'message' => 'Category not found.',
                        'code' => 404
                    ], 404
                );
            }
        } else {
            $category = $app['sms.category'];
        }

        $sms->setCategory($category);
        $config = $app['orm.em']->getRepository('Api\Sms\Entity\Config')->findOneBy(
            ['category' => $category, 'user' => $user]
        );

        if ($config) {
            $sms->setGateway($config->getGateway());
        } else {
            $sms->setGateway($app['sms.gateway']);
        }

        $app['orm.em']->persist($sms);
        $app['orm.em']->flush();

        if ($sendNow) {
            $app['sms.send']($sms);
        }

        return new JsonResponse($sms->toArray());
    }

    public function info(Application $app, $id)
    {
        if (null === $sms = $app['orm.em']->find('Api\Sms\Entity\Sms', $id)) {
            throw new \HttpException(sprintf('Sms %d does not exist', $id));
        }

        $app['sms.status']($sms);

        return new JsonResponse($sms->toArray());
    }

    public function response(Application $app, $id)
    {
        if (null === $sms = $app['orm.em']->find('Api\Sms\Entity\Sms', $id)) {
            throw new \Exception(sprintf('Sms %d does not exist', $id));
        }

        $app['sms.response']($sms);

        $responses = array();
        foreach ($app['orm.em']->getRepository('\Api\Sms\Entity\Sms')->findBy(['sms' => $sms]) as $smsResponse) {
            $responses[] = $smsResponse->toArray();
        }
        return new JsonResponse($responses);
    }
}
