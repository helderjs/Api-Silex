<?php

namespace Api\Sms;

use Api\Sms\Controller\CategoryController;
use Api\Sms\Controller\MessageController;
use Api\Sms\Provider\SmsProvider;
use Silex\Application;
use Silex\ControllerProviderInterface;
use UnidadeDigital\Sms\Controller\SendController;

/**
 * Class Importation
 *
 * Classe de definição do módulo de sms
 *
 * @package Api\Sms
 */
class Sms implements ControllerProviderInterface
{
    /**
     * Definie os controllers do módulo
     *
     * @param Application $app
     * @return \Silex\ControllerCollection
     */
    public function connect(Application $app)
    {
        $app->register(new SmsProvider());

        // Pega o gerenciador de controllers do silex
        $routing = $app['controllers_factory'];

        // Chama o método de difinição de rotas de cada controller
        CategoryController::createRoutes($routing);
        MessageController::createRoutes($routing);

        return $routing;
    }
}
