<?php
$app['app.root'] = realpath(__DIR__ . '/../');

$app['sms.gateway.config'] = [
    'Conectta' => [
        'username' => '',
        'password' => '',
    ],
    'Zenvia' => [
        'username' => '',
        'password' => '',
    ],
    'Zombie' => [],
];

$app['db.options'] = $app['app.database.config']['default'];

$app["orm.proxies_dir"] = realpath(__DIR__ . "/../var/proxies");
$app["orm.em.options"] = array(
    "mappings" => array(
        array(
            "type" => "annotation",
            "namespace" => "Olaria\\Sms\\Entity",
            "path" => realpath(__DIR__ . "/../src/"),
        ),
    ),
);
