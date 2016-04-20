<?php

define('ROOT_DIR', realpath(dirname(__FILE__) . '/../'));

// Composer autoloading

if (!file_exists(ROOT_DIR . '/vendor/autoload.php')) {
    die('Please do \'composer install\'!');
}
$loader = require_once ROOT_DIR . '/vendor/autoload.php';

// DI Container

$container = require_once ROOT_DIR . '/src/Dime/Server/bootstrap.php';

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
