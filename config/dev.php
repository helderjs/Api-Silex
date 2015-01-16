<?php

// configure your app for the development environment

use Silex\Provider\MonologServiceProvider;
use Silex\Provider\WebProfilerServiceProvider;

// include the prod configuration
require __DIR__ . '/prod.php';

// enable the debug mode
$app['debug'] = true;

$app->register(
    new MonologServiceProvider(),
    array(
        'monolog.logfile' => realpath(__DIR__ . '/../var/logs/silex_dev.log'),
    )
);

$app->register(
    new WebProfilerServiceProvider(),
    array(
        'profiler.cache_dir' => __DIR__ . '/../var/cache/profiler',
    )
);
