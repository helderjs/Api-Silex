<?php
$app['app.database.config'] = [
    'default' => [
        'driver' => 'pdo_mysql',
        'host' => 'localhost',
        'dbname' => 'api_silex',
        'user' => 'root',
        'password' => 'dev123',
        'charset' => 'utf8',
    ],
    'test' => [
        'driver' => 'pdo_mysql',
        'host' => 'localhost',
        'dbname' => 'api_silex_test',
        'user' => 'root',
        'password' => 'dev123',
        'charset' => 'utf8',
    ],
];
