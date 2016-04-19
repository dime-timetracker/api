<?php

define('ROOT_DIR', realpath(dirname(__FILE__) . '/../'));

// Composer autoloading

if (!file_exists(ROOT_DIR . '/vendor/autoload.php')) {
    die('Please do \'composer install\'!');
}
$loader = require_once ROOT_DIR . '/vendor/autoload.php';

use Interop\Container\ContainerInterface;

// Configuration
$settings = [
    'displayErrorDetails' => true,
    'routes' => [
        'resource_get' => [
            'route' => '/api/{resource}/{id:\d+}',
            'endpoint' => 'Dime\Server\Endpoint\ResourceGet',
            'map' => ['GET'],
            'middleware' => [
                'Dime\Server\Middleware\ResourceType'
            ]
        ],
        'resource_list' => [
            'route' => '/api/{resource}',
            'endpoint' => 'Dime\Server\Endpoint\ResourceList',
            'map' => ['GET'],
            'middleware' => [
                'Dime\Server\Middleware\ResourceType'
            ]
        ],
        'resource_post' => [
            'route' => '/api/{resource}',
            'endpoint' => 'Dime\Server\Endpoint\ResourcePost',
            'map' => ['POST'],
            'middleware' => [
                'Dime\Server\Middleware\ResourceType'
            ]
        ],
        'resource_put' => [
            'route' => '/api/{resource}/{id:\d+}',
            'endpoint' => 'Dime\Server\Endpoint\ResourcePut',
            'map' => ['PUT'],
            'middleware' => [
                'Dime\Server\Middleware\ResourceType'
            ]
        ],
        'resource_delete' => [
            'route' => '/api/{resource}/{id:\d+}',
            'endpoint' => 'Dime\Server\Endpoint\ResourceDelete',
            'map' => ['DELETE'],
            'middleware' => [
                'Dime\Server\Middleware\ResourceType'
            ]
        ]
    ]
];

$container = new \Slim\Container(['settings' => $settings]);

$container['connection'] = function (ContainerInterface $container) {
    $connection = \Doctrine\DBAL\DriverManager::getConnection([
        'dbname' => 'Dime',
        'user' => 'root',
        'password' => '',
        'host' => 'localhost',
        'charset' => 'utf8',
        'driver' => 'pdo_mysql'
    ], new \Doctrine\DBAL\Configuration());

    $platform = $connection->getDatabasePlatform();
    $platform->registerDoctrineTypeMapping('enum', 'string');
    return $connection;
};

$container['metadata'] = function (ContainerInterface $container) {
    return Dime\Server\Metadata\Metadata::with($container->get('connection')->getSchemaManager());
};

$container['repository'] = function (ContainerInterface $container) {
    return new Dime\Server\Entity\ResourceRepository($container->get('connection'));
};

// Middlware

$container['Dime\Server\Middleware\ResourceType'] = function (ContainerInterface $container) {
    return new Dime\Server\Middleware\ResourceType($container->get('metadata')->resources());
};

// Endpoint

$container['Dime\Server\Endpoint\ResourceGet'] = function (ContainerInterface $container) {
    return new Dime\Server\Endpoint\ResourceGet($container->get('connection'));
};

$container['Dime\Server\Endpoint\ResourceList'] = function (ContainerInterface $container) {
    return new Dime\Server\Endpoint\ResourceList($container->get('connection'));
};

$container['Dime\Server\Endpoint\ResourcePost'] = function (ContainerInterface $container) {
    return new Dime\Server\Endpoint\ResourcePost($container->get('repository'));
};

$container['Dime\Server\Endpoint\ResourcePut'] = function (ContainerInterface $container) {
    return new Dime\Server\Endpoint\ResourcePut($container->get('repository'));
};

$container['Dime\Server\Endpoint\ResourceDelete'] = function (ContainerInterface $container) {
    return new Dime\Server\Endpoint\ResourceDelete($container->get('connection'));
};

// Bootstrap routes
$app = new Slim\App($container);
Dime\Server\Stream\Stream::of($container['settings']['routes'])->each(function($config, $name) use ($app) {

    $r = $app->map(
            $config['map'], $config['route'], $config['endpoint']
    );
    $r->setName($name);


    if (isset($config['middleware'])) {
        foreach ($config['middleware'] as $mw) {
            $r = $r->add($mw);
        }
    }
});
$app->run();
