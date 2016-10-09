<?php

define('ROOT_DIR', realpath(dirname(__FILE__) . '/../'));

// Composer autoloading

if (!file_exists(ROOT_DIR . '/vendor/autoload.php')) {
    die('Please do \'composer install\'!');
}
require_once ROOT_DIR . '/vendor/autoload.php';

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\NotFoundException;
use Slim\Exception\SlimException;

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

$app = new \Dime\Server\App($settings);

// Authentication routes

$app->post('/login', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
    $login = $request->getParsedBody();

    $user = $this->get('users_repository')->find([
        new \Dime\Server\Scope\WithScope([ 'username' => $login['username']])
    ]);
    if (!$this->get('security')->authenticate($user, $login['password'])) {
        return $this->get('responder')->respond($response, ['message' => 'Bad password.'], 401);
    }

    $identifier = [ 'user_id' => $user['id'], 'client' => $login['client'] ];

    $access = $this->get('access_repository')->find([
        new \Dime\Server\Scope\WithScope($identifier)
    ]);
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
        new \Dime\Server\Scope\WithScope([ 'username' => $username ])
    );
    if (!empty($user)) {
        $this->get('access_repository')->delete([
            'user_id' => $user['id'],
            'client' => $client
        ]);
    }

    return $response;
})->add('middleware.authorization');

$app->post('/register', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
    $parsedData = $request->getParsedBody();

    if (empty($parsedData['username'])) {
        throw new \Exception('No data');
    }

    $repository = $this->get('users_repository');
    $user = $repository->find([
        new \Dime\Server\Scope\WithScope([ 'username' => $parsedData['username'] ])
    ]);
    if (!empty($user)) {
        throw new \Exception('Username is already in use.');
    }
    $userData = [
        'username'  => $parsedData['username'],
        'email'     => $parsedData['email'],
        'firstname' => $parsedData['firstname'],
        'lastname'  => $parsedData['lastname'],
        'enabled'   => true
    ];
    $this->get('security')->addUserCredentials(
        $userData,
        $parsedData['password'],
        $this->get('timeslices_repository')->count() // some unknown number
    );
    $user = \Dime\Server\Stream::of($userData)
        ->append(new \Dime\Server\Behavior\Timestampable())
        ->collect();

    $repository->insert($user);

    return $response;
});


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
})->add('middleware.authorization')
  ->add('middleware.resource');

// API routes

$app->group('/api', function () {

    $this->get('/{resource}/{id:\d+}', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
        $repository = $this->get($args['resource'] . '_repository');
        $identifier = [
            'id' => $args['id'],
            'user_id' => $this->get('session')->getUserId()
        ];

        // Select
        $result = $repository->find([
            new \Dime\Server\Scope\WithScope($identifier),
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
            new \Dime\Server\Scope\WithScope(['user_id' => $this->get('session')->getUserId()]),
            new \Dime\Server\Scope\PaginationScope($page, $with)
        ]);

        try {
            $result = $repository->findAll($scopes);
        } catch (\Exception $ex) {
            if ($this->get('settings')['displayErrorDetails']) {
                $response->getBody()->write($ex->getMessage());
            }
            throw new SlimException($request, $response->withStatus(500));
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
            throw new \Exception("No data recieved.");
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
            throw new \Exception("No data", $e->getCode(), $e);
        }

        $identity = [
            'id' => $id,
            'user_id' => $this->get('session')->getUserId()
        ];

        $result = $repository->find([
            new \Dime\Server\Scope\WithScope($identity)
        ]);

        return $this->get('responder')->respond($response, $result);

    })->setName('resource_list');

    $this->put('/{resource}/{id:\d+}', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
        $repository = $this->get($args['resource'] . '_repository');
        $identifier = [
            'id' => $args['id'],
            'user_id' => $this->get('session')->getUserId()
        ];

        $result = $repository->find([
            new \Dime\Server\Scope\WithScope($identifier)
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
            new \Dime\Server\Scope\WithScope($identifier)
        ]);

        return $this->get('responder')->respond($response, $result);

    });

    $this->delete('/{resource}/{id:\d+}', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
        $repository = $this->get($args['resource'] . '_repository');
        $identifier = [
            'id' => $args['id'],
            'user_id' => $this->get('session')->getUserId()
        ];

        // Select
        $result = $repository->find([
            new \Dime\Server\Scope\WithScope($identifier)
        ]);
        if ($result === FALSE) {
            throw new NotFoundException($request, $response);
        }

        // Delete
        $repository->delete($identifier);

        return $this->get('responder')->respond($response, $result);
    });

    $this->post('/invoice/html', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
        $parsedData = $request->getParsedBody();
        $renderer = new \Dime\InvoiceRenderer\Renderer();
        $html = $renderer->setTemplate(ROOT_DIR . '/vendor/dime-timetracker/invoice-renderer/templates/default.twig')->html($parsedData);
        $body = $response->getBody();
        $body->write($html);
    });

})->add('middleware.authorization')
  ->add('middleware.resource');


$app->run();
