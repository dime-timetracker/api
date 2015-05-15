<?php

define('ROOT_DIR', realpath(dirname(__FILE__) . '/../' ));
require_once ROOT_DIR . '/vendor/autoload.php';

use Dime\Server\Config\SlimLoader;
use Illuminate\Database\Capsule\Manager as Capsule;
use Slim\Slim;

$app = new Slim();

// start framework configuration
$app->container->singleton('configuration', function() use ($app) {
    return new SlimLoader($app);
});
$app->configuration->import(ROOT_DIR . '/app/config.yml')->refresh();

$app->config(
    'log.writer', new Dime\Server\Log\Writer(array('path' => $app->config('log_dir')))
);

// database
$configuration = $app->config('database');
if (!empty($configuration)) {
    $app->container->singleton('database', function () {
        return new Capsule();
    });
    $app->database->addConnection($configuration);
    $app->database->setAsGlobal();
    $app->database->bootEloquent();
} else {
    $app->log->error('Database: No configuration [database] found.');
}


// load controller
foreach($app->config('routes') as $class) {
    $ref = new ReflectionClass($class);
    if ($ref->implementsInterface('Dime\Server\Controller\SlimController')) {
        /**
         * @var Controller $controller
         */
        $controller = new $class();
        $controller->enable($app);
        $app->log->debug('Register and enable controller [' . $class . '].');
    }
}

$app->run();
