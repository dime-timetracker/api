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
use Slim\Exception\NotFoundException;

// Configuration

$configuration = [];
if (file_exists(ROOT_DIR . '/config/parameters.php')) {
    $configuration = require_once ROOT_DIR . '/config/parameters.php';
}
$settings = array_replace_recursive(
    [
        'displayErrorDetails' => true,
        'enableSecurity' => false,
        'allowedResources' => [
            'activities', 'timeslices', 'customers', 'projects', 'services', 'settings', 'tags'
        ]
    ],
    $configuration
);

// DI Container

$container = new \Slim\Container(['settings' => $settings]);

// Dependencies

$container['connection'] = function (ContainerInterface $container) {
    $connection = \Doctrine\DBAL\DriverManager::getConnection($container['settings']['database'], new \Doctrine\DBAL\Configuration());

    $platform = $connection->getDatabasePlatform();
    $platform->registerDoctrineTypeMapping('enum', 'string');
    return $connection;
};

$container['metadata'] = function (ContainerInterface $container) {
    return new \Dime\Server\Metadata($container->get('connection')->getSchemaManager());
};

$container['session'] = function () {
    return new \Dime\Server\Session();
};

$container['responder'] = function () {
    return new \Dime\Server\Responder\JsonResponder();
};

$container['uri'] = function (ContainerInterface $container) {
    return new \Dime\Api\Uri($container->get('router'), $container->get('environment'));
};

$container['security'] = function () {
    return new \Dime\Api\SecurityProvider();
};

// Middleware

$container['Dime\Server\Middleware\Authorization'] = function (ContainerInterface $container) {
    if (!$container->get('settings')['enableSecurity']) {
        return new \Dime\Server\Middleware\Pass();
    }

    $accessRepository = $container->get('access_repository');

    $users = $container->get('users_repository')->findAll([
        new \Dime\Server\Scope\With(['enabled' => true])
    ]);

    $access = \Dime\Server\Stream::of($users)
        ->remap(function ($value, $key) {
            return $value['username'];
        })
        ->map(function ($value, $key) use ($accessRepository) {
            $accessData = $accessRepository->findAll([
                new \Dime\Server\Scope\With(['user_id' => $value['id']])
            ]);
            return \Dime\Server\Stream::of($accessData)
                    ->map(function (array $value, $key) {
                        $value['expires'] = $value['updated_at'];
                        unset($value['created_at']);
                        unset($value['updated_at']);

                        return $value;
                    })
                    ->collect();
        })
        ->collect();

    return new \Dime\Server\Middleware\Authorization(
        $container->get('session'),
        $container->get('responder'),
        $access
    );
};

$container['Dime\Server\Middleware\ResourceType'] = function (ContainerInterface $container) {
    return new \Dime\Server\Middleware\ResourceType($container->get('settings')['allowedResources']);
};

// Repositories

$container['access_repository'] = function (ContainerInterface $container) {
    return new \Dime\Server\Repository($container->get('connection'), 'access');
};
$container['activities_repository'] = function (ContainerInterface $container) {
    return new \Dime\Api\Repository\Activities($container->get('connection'));
};
$container['activities_filter'] = function () {
    return new \Dime\Server\Filter([
        new \Dime\Server\Filter\Relation('customer'),
        new \Dime\Server\Filter\Relation('project'),
        new \Dime\Server\Filter\Relation('service'),
        new \Dime\Api\Filter\TimesliceDate(),
        new \Dime\Server\Filter\Search(),
    ]);
};
$container['customers_repository'] = function (ContainerInterface $container) {
    return new \Dime\Server\Repository($container->get('connection'), 'customers');
};
$container['customers_validator'] = function () {
    return new \Dime\Server\Validator([
        'required' => new \Dime\Server\Validator\Required(['alias'])
    ]);
};
$container['projects_repository'] = function (ContainerInterface $container) {
    return new \Dime\Server\Repository($container->get('connection'), 'projects');
};
$container['projects_filter'] = function () {
    return new \Dime\Server\Filter([
        new \Dime\Server\Filter\Relation('customer'),
        new \Dime\Server\Filter\Date(),
        new \Dime\Server\Filter\Search(['name', 'description']),
    ]);
};
$container['projects_validator'] = function () {
    return new \Dime\Server\Validator([
        'required' => new \Dime\Server\Validator\Required(['alias'])
    ]);
};
$container['services_repository'] = function (ContainerInterface $container) {
    return new \Dime\Server\Repository($container->get('connection'), 'services');
};
$container['services_validator'] = function () {
    return new \Dime\Server\Validator([
        'required' => new \Dime\Server\Validator\Required(['alias'])
    ]);
};
$container['settings_repository'] = function (ContainerInterface $container) {
    return new \Dime\Server\Repository($container->get('connection'), 'settings');
};
$container['services_validator'] = function () {
    return new \Dime\Server\Validator([
        'required' => new \Dime\Server\Validator\Required(['name', 'value'])
    ]);
};
$container['tags_repository'] = function (ContainerInterface $container) {
    return new \Dime\Server\Repository($container->get('connection'), 'tags');
};
$container['tags_validator'] = function () {
    return new \Dime\Server\Validator([
        'required' => new \Dime\Server\Validator\Required(['name'])
    ]);
};
$container['timeslices_repository'] = function (ContainerInterface $container) {
    return new \Dime\Api\Repository\Timeslices($container->get('connection'));
};
$container['timeslices_filter'] = function () {
    return new \Dime\Server\Filter([
        new \Dime\Server\Filter\Relation('activity'),
        new \Dime\Server\Filter\Date(['start' => 'started_at', 'end' => 'stopped_at']),
    ]);
};
$container['timeslices_validator'] = function () {
    return new \Dime\Server\Validator([
        'required' => new \Dime\Server\Validator\Required(['activity_id', 'duration'])
    ]);
};
$container['users_repository'] = function (ContainerInterface $container) {
    return new \Dime\Server\Repository($container->get('connection'), 'users');
};

$container['assignable'] = function (ContainerInterface $container) {
    return new \Dime\Server\Behavior\Assignable($container->get('session')->getUserId());
};

// App

$app = new \Slim\App($container);

// Authentication routes

$app->post('/login', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
    $login = $request->getParsedBody();

    $user = $this->get('users_repository')->find(['username' => $login['username']]);
    if (!$this->get('security')->authenticate($user, $login['password'])) {
        return $this->get('responder')->respond($response, ['message' => 'Bad password.'], 401);
    }

    $identifier = [ 'user_id' => $user['id'], 'client' => $login['client'] ];

    $access = $this->get('access_repository')->find($identifier);
    if (empty($access)) {
        $access = $identifier;
        $access['token'] = $this->get('security')->createToken($user['id'], $login['client']);

        $access = \Dime\Server\Stream::of($access)
                ->append(new \Dime\Server\Behavior\Timestampable())
                ->collect();

        $this->get('access_repository')->insert($access);
    } else {
        $access['token'] = $this->get('security')->createToken($user['id'], $login['client']);

        $access = \Dime\Server\Stream::of($access)
                ->append(new \Dime\Server\Behavior\Timestampable(null))
                ->collect();

        $this->get('access_repository')->update($access, $identifier);
    }

    return $this->get('responder')->respond($response, [
        'token' => $access['token'],
        'expires' => $this->get('security')->expires($access['updated_at'])
    ]);
});

$app->post('/logout', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
    $username = $request->getAttribute('username');
    $client = $request->getAttribute('client');

    if (empty($username) || empty($client)) {
        throw new NotFoundException($request, $response);
    }

    $user = $this->get('users_repository')->find(
        new \Dime\Server\Scope\With([ 'username' => $username ])
    );
    if (!empty($user)) {
        $this->get('access_repository')->delete([
            'user_id' => $user['id'],
            'client' => $client
        ]);
    }

    return $response;
})->add('Dime\Server\Middleware\Authorization');

$app->get('/apidoc[/{resource}]', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
    $metadata = $this->get('metadata');

    if (array_key_exists('resource', $args)) {
        if (!$metadata->hasResource($args['resource'])) {
            throw new NotFoundException($request, $response);
        }

        $result = \Dime\Server\Stream::of($metadata->resource($args['resource'])->getColumns())->map(function ($value, $key) {
            return $value->getType()->getName();
        })->collect();
    } else {
        $result = $this->get('settings')['allowedResources'];
    }

    return $this->get('responder')->respond($response, $result);
})->add('Dime\Server\Middleware\Authorization')
  ->add('Dime\Server\Middleware\ResourceType');

// API routes

$app->group('/api', function () {

    $this->get('/{resource}/{id:\d+}', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
        $repository = $this->get($args['resource'] . '_repository');
        $identifier = [
            'id' => filter_var($args['id'], FILTER_SANITIZE_NUMBER_INT),
            'user_id' => $this->get('session')->getUserId()
        ];

        // Select
        $result = $repository->find([
            new \Dime\Server\Scope\With($identifier),
        ]);

        if ($result === FALSE) {
            throw new NotFoundException($request, $response);
        }

        return $this->get('responder')->respond($response, $result);
    });

    $this->get('/{resource}', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
        $repository = $this->get($args['resource'] . '_repository');
        $page = $this->get('uri')->getQueryParam($request, 'page', 1);
        $with = $this->get('uri')->getQueryParam($request, 'with',  100);
        $by = $this->get('uri')->getQueryParam($request, 'by', []);

        $filter = [];
        if ($this->has($args['resource'] . '_filter')) {
            $filter = $this->get($args['resource'] . '_filter')->build($by);
        }

        $scopes = array_merge($filter, [
            new \Dime\Server\Scope\With(['user_id' => $this->get('session')->getUserId()]),
            new \Dime\Server\Scope\Pagination($page, $with)
        ]);

        try {
            $result = $repository->findAll($scopes);
        } catch (\Exception $ex) {
            throw new NotFoundException($request, $response);
        }

        // add header X-Dime-Total and Link
        $total = $repository->count($scopes);
        $link = $this->get('uri')->buildLinkHeader($request, $total, $page, $with);

        return $this->get('responder')->respond(
            $response
                ->withHeader("X-Dime-Total", $total)
                ->withHeader('Link', implode(', ', $link)),
            $result
        );
    });

    $this->post('/{resource}', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
        $repository = $this->get($args['resource'] . '_repository');

        $parsedData = $request->getParsedBody();
        if (empty($parsedData)) {
            throw new \Exception("No data");
        }

        // Tranform and behave
        $behavedData = \Dime\Server\Stream::of($parsedData)
                ->append(new \Dime\Server\Behavior\Timestampable())
                ->append($this->get('assignable'))
                ->collect();

        // Validate
        if ($this->has($args['resource'] . '_validator')) {
            $errors = $this->get($args['resource'] . '_validator')->validate($behavedData);
            if (!empty($errors)) {
                return $this->get('responder')->respond($response, $errors, 400);
            }
        }

        try {
            $id = $repository->insert($behavedData);
        } catch (\Exception $e) {
            throw new \Exception("No data");
        }

        $identity = [
            'id' => $id,
            'user_id' => $this->get('session')->getUserId()
        ];

        $result = $repository->find([
            new \Dime\Server\Scope\With($identity)
        ]);

        return $this->get('responder')->respond($response, $result);

    })->setName('resource_list');

    $this->put('/{resource}/{id:\d+}', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
        $repository = $this->get($args['resource'] . '_repository');
        $identifier = [
            'id' => filter_var($args['id'], FILTER_SANITIZE_NUMBER_INT),
            'user_id' => $this->get('session')->getUserId()
        ];

        $result = $repository->find([
            new \Dime\Server\Scope\With($identifier)
        ]);
        if ($result === FALSE) {
            throw new NotFoundException($request, $response);
        }

        $parsedData = $request->getParsedBody();
        if (empty($parsedData)) {
            throw new \Exception("No data");
        }

        // Tranform and behave
        $behavedData = \Dime\Server\Stream::of($parsedData)
                ->append(new \Dime\Server\Behavior\Timestampable(null))
                ->append($this->get('assignable'))
                ->collect();

        // Validate
        if ($this->has($args['resource'] . '_validator')) {
            $errors = $this->get($args['resource'] . '_validator')->validate($behavedData);
            if (!empty($errors)) {
                return $this->get('responder')->respond($response, $errors, 400);
            }
        }

        try {
            $repository->update($behavedData, $identifier);
        } catch (\Exception $e) {
            var_dump($e);
        }

        $result = $repository->find([
            new \Dime\Server\Scope\With($identifier)
        ]);

        return $this->get('responder')->respond($response, $result);

    });

    $this->delete('/{resource}/{id:\d+}', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
        $repository = $this->get($args['resource'] . '_repository');
        $identifier = [
            'id' => filter_var($args['id'], FILTER_SANITIZE_NUMBER_INT),
            'user_id' => $this->get('session')->getUserId()
        ];

        // Select
        $result = $repository->find([
            new \Dime\Server\Scope\With($identifier)
        ]);
        if ($result === FALSE) {
            throw new NotFoundException($request, $response);
        }

        // Delete
        $repository->delete($identifier);

        return $this->get('responder')->respond($response, $result);
    });

})->add('Dime\Server\Middleware\Authorization')
  ->add('Dime\Server\Middleware\ResourceType');


$app->run();