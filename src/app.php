<?php

use Silex\Application;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
use Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

// Cria uma instancia da aplicação silex e registra compoenentes
$app = new Application();
$app->register(new UrlGeneratorServiceProvider());
$app->register(new ValidatorServiceProvider());
$app->register(new ServiceControllerServiceProvider());
$app->register(new SessionServiceProvider());
$app->register(
    new Silex\Provider\TwigServiceProvider(),
    array(
        'twig.path' => __DIR__ . '/../views',
    )
);
$app->register(new DoctrineServiceProvider());
$app->register(new DoctrineOrmServiceProvider());

// Define a rota do módulo importação
$app->mount('/sms', new Api\Sms\Sms());

$app->error(
    function (\Exception $e, $code) {
        if ($e->getCode() != 0) {
            $code = $e->getCode();
        }
        return new \Symfony\Component\HttpFoundation\JsonResponse(
            [
                'success' => false,
                'error' => $e->getMessage(),
            ], $code
        );
    }
);

$app->before(
    function (Request $request) use ($app) {
        if (!$request->get('key', false)) {
            return new JsonResponse(['error' => true, 'message' => 'Authorization key required.', 'code' => 403], 403);
        }

        $app['user'] = $app['orm.em']->getRepository('Api\Sms\Entity\User')->findOneBy(
            ['access_key' => $request->get('key')]
        );

        $host =  $app['orm.em']->getRepository('Api\Sms\Entity\Host')->findOneBy(
            ['user' => $app['user'], 'ip' => $request->getClientIp(), 'name' => $request->getHost()]
        );

        if (!$host) {
            return new JsonResponse([
                    'error' => true,
                    'message' => 'You must access by an authorized host.',
                    'code' => 403
                ], 403);
        }

        if (!$app['user']) {
            return new JsonResponse(
                [
                    'error' => true,
                    'message' => 'A valid authorization key required.',
                    'code' => 403
                ], 403
            );
        }
    }
);

return $app;
