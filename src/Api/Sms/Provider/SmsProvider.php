<?php
/**
 * Created by PhpStorm.
 * User: helder
 * Date: 27/06/14
 * Time: 14:51
 */

namespace Api\Sms\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Api\Sms\Entity\Sms;

class SmsProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['sms.initializer'] = $app->protect(
            function () use ($app) {
                static $initialized = false;

                if ($initialized) {
                    return;
                }

                $initialized = true;

                $category = $app['orm.em']->getRepository('Api\Sms\Entity\Category')
                    ->findOneBy(['name' => 'default', 'user' => $app['user']]);

                $config = $app['orm.em']->getRepository('Api\Sms\Entity\Config')
                    ->findOneBy(['category' => $category, 'user' => $app['user']]);

                $app['sms.defaults.user'] = $app['user'];
                $app['sms.defaults.category'] = $category;
                $app['sms.defaults.config'] = $config;
                $app['sms.default'] = $config->getGateway()->getService();
            }
        );

        $app['sms.gateways'] = $app->share(
            function (Application $app) {

                $gateways = new \Pimple();
                foreach ($app['orm.em']->getRepository('Api\Sms\Entity\Gateway')->findAll() as $gateway) {
                    $gateways[$gateway->getService()] = $gateway;
                }

                return $gateways;
            }
        );

        $app['sms.gateway'] = $app->share(
            function (Application $app) {
                $app['sms.initializer']();

                $gateways = $app['sms.gateways'];

                return $gateways[$app['sms.default']];
            }
        );

        $app['sms.category'] = $app->share(
            function (Application $app) {
                $app['sms.initializer']();

                return $app['sms.defaults.category'];
            }
        );

        $app['sms.send'] = $app->protect(
            function (Sms $sms) use ($app) {
                $gateway = $sms->getGateway()->getService();
                $class = "\\Api\\Sms\\Service\\Gateway\\{$gateway}";

                $service = new $class($app['sms.gateway.config'][$gateway]);

                $status = $service->send($sms);
                $sms->setStatus($status);
                $app['orm.em']->flush();

                return $sms;
            }
        );

        $app['sms.status'] = $app->protect(
            function (Sms $sms) use ($app) {
                $gateway = $sms->getGateway()->getService();
                $class = "\\Api\\Sms\\Service\\Gateway\\{$gateway}";

                $service = new $class($app['sms.gateway.config'][$gateway]);

                $status = $service->status($sms);
                $sms->setStatus($status);
                $app['orm.em']->flush();

                return $sms;
            }
        );

        $app['sms.response'] = $app->protect(
            function (Sms $sms) use ($app) {
                $gateway = $sms->getGateway()->getService();
                $class = "\\Api\\Sms\\Service\\Gateway\\{$gateway}";

                $service = new $class($app['sms.gateway.config'][$gateway]);

                $data = $service->responseSms($sms);
                foreach ($data as $response) {
                    if (!$app['orm.em']->getRepository('\Api\Sms\Entity\Sms')->findOneBy(
                        [
                            'ddd' => $response->getDdd(),
                            'number' => $response->getNumber(),
                            'signature' => $response->getSignature(),
                            'status' => $response->getStatus(),
                            'type' => $response->getType(),
                            'sms' => $response->getSms()
                        ]
                    )
                    ) {
                        $app['orm.em']->persist($response);
                    }
                }
                $app['orm.em']->flush();

                return $data;
            }
        );
    }

    public function boot(Application $app)
    {
    }
}
