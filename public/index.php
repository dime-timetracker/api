<?php

define('ROOT_DIR', realpath(dirname(__FILE__) . '/../'));

// Composer autoloading

if (!file_exists(ROOT_DIR . '/vendor/autoload.php')) {
    die('Please do \'composer install\'!');
}
require_once ROOT_DIR . '/vendor/autoload.php';


use Interop\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

// Configuration

$configuration = [];
if (file_exists(ROOT_DIR . '/config/parameters.php')) {
    $configuration = require_once ROOT_DIR . '/config/parameters.php';
}
$settings = array_replace_recursive(
    [
        'displayErrorDetails' => true
    ],
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
    return Dime\Server\Metadata::with($container->get('connection')->getSchemaManager());
};

$container['responder'] = function () {
    return new Dime\Server\Responder\JsonResponder();
};


$container['Dime\Server\Middleware\Authorization'] = function (ContainerInterface $container) {
    $access = [];
    return new Dime\Server\Middleware\Authorization($container->get('responder'), $access);
};

$container['Dime\Server\Middleware\ResourceType'] = function (ContainerInterface $container) {
    return new Dime\Server\Middleware\ResourceType($container->get('metadata')->resources());
};


$container['behavior_post'] = function (ContainerInterface $container) {
    $behavior = new \Dime\Server\Behavior();
    $behavior->add(new \Dime\Server\Behavior\Timestampable());
    $behavior->add(new \Dime\Server\Behavior\Assignable());
    return $behavior;
};
$container['behavior_put'] = function (ContainerInterface $container) {
    $behavior = new \Dime\Server\Behavior();
    $behavior->add(new \Dime\Server\Behavior\Timestampable(null));
    $behavior->add(new \Dime\Server\Behavior\Assignable());
    return $behavior;
};
$container['validator'] = function (ContainerInterface $container) {
    return new Dime\Server\Validator();
};

$container['activities_repository'] = function (ContainerInterface $container) {
    return new Dime\Server\Repository\ResourceRepository($container->get('connection'), 'activities');
};

$container['customers_repository'] = function (ContainerInterface $container) {
    return new Dime\Server\Repository\ResourceRepository($container->get('connection'), 'customers');
};
$container['projects_repository'] = function (ContainerInterface $container) {
    return new Dime\Server\Repository\ResourceRepository($container->get('connection'), 'projects');
};
$container['services_repository'] = function (ContainerInterface $container) {
    return new Dime\Server\Repository\ResourceRepository($container->get('connection'), 'services');
};
$container['settings_repository'] = function (ContainerInterface $container) {
    return new Dime\Server\Repository\ResourceRepository($container->get('connection'), 'settings');
};
$container['tags_repository'] = function (ContainerInterface $container) {
    return new Dime\Server\Repository\ResourceRepository($container->get('connection'), 'settings');
};

// App

$app = new \Slim\App($container);

// Authenication
$app->put('login', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
});

$app->put('logout', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
})->add('Dime\Server\Middleware\Authorization');

// API
$app->group('/api', function () {

    $this->get('/{resource}/{id:\d+}', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
        $repository = $this->get($args['resource'] . '_repository');
        $identifier = ['id' => filter_var($args['id'], FILTER_SANITIZE_NUMBER_INT)];

        // Select
        $result = $repository->find($identifier);
        if ($result === FALSE) {
            throw new NotFoundException($request, $response);
        }

        // TODO Filter and extend data

        return $this->get('responder')->respond($response, $result);
    });

    $this->get('/{resource}', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
        $repository = $this->get($args['resource'] . '_repository');
        
        return $this->get('responder')->respond($response, $repository->findAll());
    });

    $this->post('/{resource}', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
        $repository = $this->get($args['resource'] . '_repository');
        
        $parsedData = $request->getParsedBody();
        if (empty($parsedData)) {
            throw new \Exception("No data");
        }

        // Behave
        $behavedData = $this->get('behavior_post')->execute($parsedData);

        // Validate
        $validator = $this->get('validator');
        if (!$validator->validate($behavedData)) {
            return $this->get('responder')->respond($response, $validator->getErrors(), 400);
        }

        try {
            $id = $repository->insert($behavedData);
        } catch (\Exception $e) {
            var_dump($e);
        }

        return $this->get('responder')->respond($response, $repository->find(['id' => $id]));

    });

    $this->put('/{resource}/{id:\d+}', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
        $repository = $this->get($args['resource'] . '_repository');
        $identifier = [
            'id' => filter_var($args['id'], FILTER_SANITIZE_NUMBER_INT)
        ];

        $result = $repository->find($identifier);
        if ($result === FALSE) {
            throw new NotFoundException($request, $response);
        }

        $parsedData = $request->getParsedBody();
        if (empty($parsedData)) {
            throw new \Exception("No data");
        }

        // Behave
        $behavedData = $this->get('behavior_put')->execute($parsedData);

        // Validate
        $validator = $this->get('validator');
        if (!$validator->validate($behavedData)) {
            return $this->get('resonder')->respond($response, $validator->getErrors(), 400);
        }

        try {
            $repository->update($behavedData, $identifier);
        } catch (\Exception $e) {
            var_dump($e);
        }

        return $this->get('resonder')->respond($response, $this->repository->find($identifier));

    });

    $this->delete('/{resource}/{id:\d+}', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
        $repository = $this->get($args['resource'] . '_repository');
        $identifier = ['id' => filter_var($args['id'], FILTER_SANITIZE_NUMBER_INT)];

        // Select
        $result = $repository->find($identifier);
        if ($result === FALSE) {
            throw new NotFoundException($request, $response);
        }

        // Delete
        $repository->delete($identifier);

        return $this->get('resonder')->respond($response, $result);
    });

})->add('Dime\Server\Middleware\ResourceType');


$app->run();