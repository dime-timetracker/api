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

$container['mediator'] = function (ContainerInterface $container) {
    return new Dime\Server\Mediator();
};

$container['responder'] = function () {
    return new Dime\Server\Responder\JsonResponder();
};

$container['uri'] = function (ContainerInterface $container) {
    return new Dime\Server\Uri($container->get('router'), $container->get('environment'));
};


$container['Dime\Server\Middleware\Authorization'] = function (ContainerInterface $container) {
    $accessRepository = $container->get('access_repository');
    
    $access = Dime\Server\Stream::of($container->get('users_repository')->findAll())
        ->remap(function ($value, $key) {
            return $value['username'];
        })
        ->map(function ($value, $key) use ($accessRepository) {
            return Dime\Server\Stream::of($accessRepository->findAll([new Dime\Server\Scope\UserScope($value['id'])]))
                    ->map(new Dime\Server\Transformer\ExpireTransformer())
                    ->collect();
        })
        ->collect();
        
    return new Dime\Server\Middleware\Authorization(
        $container->get('mediator'), 
        $container->get('responder'), 
        $access
    );
};

$container['Dime\Server\Middleware\ResourceType'] = function (ContainerInterface $container) {
    return new Dime\Server\Middleware\ResourceType($container->get('metadata')->resources());
};

$container['validator'] = function (ContainerInterface $container) {
    return new Dime\Server\Validator();
};

// Behavior
$container['assignable'] = function (ContainerInterface $container) {
    return new \Dime\Server\Behavior\Assignable($container->get('mediator')->getUserId());
};

$container['activities_repository'] = function (ContainerInterface $container) {
    return new Dime\Server\Repository\ActivitiesResourceRepository($container->get('connection'));
};
$container['activities_filter'] = function (ContainerInterface $container) {
    return [
        new Dime\Server\Scope\UserScope($container->get('mediator')->getUserId())
    ];
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
$container['access_repository'] = function (ContainerInterface $container) {
    return new Dime\Server\Repository\ResourceRepository($container->get('connection'), 'access');
};
$container['users_repository'] = function (ContainerInterface $container) {
    return new Dime\Server\Repository\ResourceRepository($container->get('connection'), 'users');
};
$container['security'] = function (ContainerInterface $container) {
    return new Dime\Server\SecurityProvider();
};

// App

$app = new \Slim\App($container);

// Authenication
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
        throw new NotFoundException();
    }

    $user = $this->get('users_repository')->find(['username' => $username]);
    if (!empty($user)) {
        $this->get('access_repository')->delete([
            'user_id' => $user['id'],
            'client' => $client
        ]);
    }
    
    return $response;
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
        
        $result = Dime\Server\Stream::of($result)
                ->remap(new Dime\Server\Transformer\CamelizeKey())
                ->collect();

        return $this->get('responder')->respond($response, $result);
    });

    $this->get('/{resource}', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
        $repository = $this->get($args['resource'] . '_repository');
        $page = $this->get('uri')->getQueryParam($request, 'page', 1);
        $with = $this->get('uri')->getQueryParam($request, 'with', 0);
        
        $result = Dime\Server\Stream::of($repository->findAll([], $page, $with))
                ->map(function ($value, $key) {
                    return Dime\Server\Stream::of($value)
                            ->remap(new Dime\Server\Transformer\CamelizeKey())
                            ->collect();
                })
                ->collect();
                
        // add header X-Dime-Total and Link
        $total = $repository->count();
        
        $lastPage = 1;
        $queryParameter = $request->getQueryParams();
        if ($with > 1) {
            $lastPage = ceil($total / $with);
            $queryParameter['with'] = $with;
        }
        $link = [];
        if ($page + 1 <= $lastPage) {
            $queryParameter['page'] =  $page + 1;
            $link[] = sprintf('<%s>; rel="next"', $this->get('uri')->pathFor(
                'resource_list',
                ['resource' => $args['resource']],
                $queryParameter
            ));
        }
        if ($page - 1 > 0) {
            $queryParameter['page'] =  $page - 1;
            $link[] = sprintf('<%s>; rel="prev"', $this->get('uri')->pathFor(
                'resource_list',
                ['resource' => $args['resource']],
                $queryParameter
            ));
        }
        if ($page != 1) {
            $queryParameter['page'] =  1;
            $link[] = sprintf('<%s>; rel="first"', $this->get('uri')->pathFor(
                'resource_list',
                ['resource' => $args['resource']],
                $queryParameter
            ));
        }
        if ($page != $lastPage) {
            $queryParameter['page'] =  $lastPage;
            $link[] = sprintf('<%s>; rel="last"', $this->get('uri')->pathFor(
                'resource_list',
                ['resource' => $args['resource']],
                $queryParameter
            ));
        }        
                
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
        $behavedData = Dime\Server\Stream::of($parsedData)
                ->remap(new Dime\Server\Transformer\DecamelizeKey())
                ->append(new \Dime\Server\Behavior\Timestampable())
                ->append($this->get('assignable'))
                ->collect();

        // Validate
        $validator = $this->get('validator');
        if (!$validator->validate($behavedData)) {
            return $this->get('responder')->respond($response, $validator->getErrors(), 400);
        }

        try {
            $id = $repository->insert($behavedData);
        } catch (\Exception $e) {
            throw new \Exception("No data");
        }

        $result = Dime\Server\Stream::of($repository->find(['id' => $id]))
                ->remap(new Dime\Server\Transformer\CamelizeKey())
                ->collect();
        
        return $this->get('responder')->respond($response, $result);

    })->setName('resource_list');

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
        
        // Tranform and behave
        $behavedData = Dime\Server\Stream::of($parsedData)
                ->remap(new Dime\Server\Transformer\DecamelizeKey())
                ->append(new \Dime\Server\Behavior\Timestampable(null))
                ->append($this->get('assignable'))
                ->collect();

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

        $result = Dime\Server\Stream::of($this->repository->find($identifier))
                ->remap(new Dime\Server\Transformer\CamelizeKey())
                ->collect();
        
        return $this->get('resonder')->respond($response, $result);

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
        
        $result = Dime\Server\Stream::of($result)
                ->remap(new Dime\Server\Transformer\CamelizeKey())
                ->collect();

        return $this->get('resonder')->respond($response, $result);
    });

})
    //->add('Dime\Server\Middleware\Authorization')
    ->add('Dime\Server\Middleware\ResourceType');


$app->run();