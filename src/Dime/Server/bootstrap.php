<?php

use Interop\Container\ContainerInterface;

// Configuration

$configuration = [];
if (file_exists(ROOT_DIR . '/config/parameters.php')) {
    $configuration = require_once ROOT_DIR . '/config/parameters.php';
}
$settings = array_replace_recursive(
    require_once __DIR__ . '/config.php',
    $configuration
);

// DI Container

$container = new \Slim\Container(['settings' => $settings]);

$container['connection'] = function (ContainerInterface $container) {
    $connection = \Doctrine\DBAL\DriverManager::getConnection($container['settings']['database'], new \Doctrine\DBAL\Configuration());

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
    return new Dime\Server\Endpoint\ResourceGet($container->get('repository'));
};

$container['Dime\Server\Endpoint\ResourceList'] = function (ContainerInterface $container) {
    return new Dime\Server\Endpoint\ResourceList($container->get('repository'));
};

$container['Dime\Server\Endpoint\ResourcePost'] = function (ContainerInterface $container) {
    return new Dime\Server\Endpoint\ResourcePost($container->get('repository'));
};

$container['Dime\Server\Endpoint\ResourcePut'] = function (ContainerInterface $container) {
    return new Dime\Server\Endpoint\ResourcePut($container->get('repository'));
};

$container['Dime\Server\Endpoint\ResourceDelete'] = function (ContainerInterface $container) {
    return new Dime\Server\Endpoint\ResourceDelete($container->get('repository'));
};