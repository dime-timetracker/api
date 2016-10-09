<?php

namespace Dime\Server;

use League\Container\Container;
use League\Container\ReflectionContainer;
use Slim\App as Slim;
use Slim\CallableResolver;
use Slim\Collection;
use Slim\Handlers\Error;
use Slim\Handlers\NotAllowed;
use Slim\Handlers\NotFound;
use Slim\Handlers\Strategies\RequestResponse;
use Slim\Http\Environment;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Interfaces\RouterInterface;
use Slim\Router;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Doctrine\DBAL\Connection;

class App extends Slim
{
    const DEFAULT_SETTINGS = [
        'httpVersion'                       => '1.1',
        'responseChunkSize'                 => 4096,
        'outputBuffering'                   => 'append',
        'determineRouteBeforeAppMiddleware' => false,
        'displayErrorDetails'               => true,
    ];

    /**
     * {@inheritdoc}
     */
    public function __construct(array $settings = [])
    {
        $container = new Container;
        $container->delegate(new ReflectionContainer);
        parent::__construct($container);
        $this->registerSlimServices($settings);
    }

    private function registerSlimServices(array $settings = [])
    {
        $this->getContainer()->share('settings', function () use ($settings) {
            return new Collection(array_replace_recursive(
                self::DEFAULT_SETTINGS,
                $settings
            ));
        });

        $this->getContainer()->share('environment', function () {
            return new Environment($_SERVER);
        });
        $this->getContainer()->add(Environment::class, 'environment');

        $this->getContainer()->share('request', function () {
            return Request::createFromEnvironment($this->getContainer()->get('environment'));
        });
        $this->getContainer()->add(ServerRequestInterface::class, 'request');

        $this->getContainer()->share('response', function () {
            $headers = new Headers(['Content-Type' => 'text/html']);
            $response = new Response(200, $headers);

            return $response->withProtocolVersion($this->getContainer()->get('settings')['httpVersion']);
        });
        $this->getContainer()->add(ResponseInterface::class, 'response');

        $this->getContainer()->share('router', function () {
            return new Router;
        });
        $this->getContainer()->share(RouterInterface::class, 'router');

        $this->getContainer()->share('foundHandler', function () {
            return new RequestResponse;
        });

        $this->getContainer()->share('errorHandler', function () {
            return new Error($this->getContainer()->get('settings')['displayErrorDetails']);
        });

        $this->getContainer()->share('notFoundHandler', function () {
            return new NotFound;
        });

        $this->getContainer()->share('notAllowedHandler', function () {
            return new NotAllowed;
        });

        $this->getContainer()->share('callableResolver', function () {
            return new CallableResolver($this->getContainer());
        });

        $this->getContainer()->add(Connection::class, 'connection');
        $this->getContainer()->share('connection', function () {
          $settings = $this->getContainer()->get('settings')['database'];
          $connection = \Doctrine\DBAL\DriverManager::getConnection($settings, new \Doctrine\DBAL\Configuration());

          $platform = $connection->getDatabasePlatform();
          $platform->registerDoctrineTypeMapping('enum', 'string');
          return $connection;
        });
        $this->getContainer()->add('metadata', \Dime\Server\Metadata::class);


        $this->getContainer()->share('uri', function () {
            return new \Dime\Server\Uri(
                $this->getContainer()->get('router'),
                $this->getContainer()->get('environment')
            );
        });

        $this->getContainer()->add('session', \Dime\Server\Session::class);
        $this->getContainer()->add('responder', \Dime\Server\Responder\JsonResponder::class);
        $this->getContainer()->add('security', \Dime\Api\SecurityProvider::class);

        $this->getContainer()->share('middleware.resource', function () {
            return new \Dime\Server\Middleware\ResourceType($this->getContainer()->get('settings')['allowedResources']);
        });

        $this->getContainer()->share('middleware.authorization', function () {
            if (!$this->getContainer()->get('settings')['enableSecurity']) {
                return new \Dime\Server\Middleware\Pass();
            }

            $accessRepository = $this->getContainer()->get('access_repository');

            $users = $this->getContainer()->get('users_repository')->findAll([
                new \Dime\Server\Scope\WithScope(['enabled' => true])
            ]);

            $access = \Dime\Server\Stream::of($users)
                ->remap(function ($value, $key) {
                    return $value['username'];
                })
                ->map(function ($value, $key) use ($accessRepository) {
                    $accessData = $accessRepository->findAll([
                        new \Dime\Server\Scope\WithScope(['user_id' => $value['id']])
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
                $this->getContainer()->get('session'),
                $this->getContainer()->get('responder'),
                $access
            );
        });

        $this->getContainer()->share('access_repository', function() {
            return new \Dime\Server\Repository($this->getContainer()->get('connection'), 'access');
        });

        $this->getContainer()->share('access_repository', function() {
            return new \Dime\Server\Repository($this->getContainer()->get('connection'), 'access');
        });
        $this->getContainer()->share('activities_repository', function() {
            return new \Dime\Api\Repository\Activities($this->getContainer()->get('connection'));
        });
        $this->getContainer()->share('activities_filter', function () {
            return new \Dime\Server\Filter([
                new \Dime\Server\Filter\RelationFilter('customer'),
                new \Dime\Server\Filter\RelationFilter('project'),
                new \Dime\Server\Filter\RelationFilter('service'),
                new \Dime\Api\Filter\TimesliceDateFilter(),
                new \Dime\Api\Filter\TagFilter(),
                new \Dime\Server\Filter\SearchFilter(),
            ]);
        });
        $this->getContainer()->share('customers_repository', function() {
            return new \Dime\Server\Repository($this->getContainer()->get('connection'), 'customers');
        });
        $this->getContainer()->share('customers_validator', function () {
            return new \Dime\Server\Validator([
                'required' => new \Dime\Server\Validator\Required(['alias'])
            ]);
        });
        $this->getContainer()->share('projects_repository', function() {
            return new \Dime\Server\Repository($this->getContainer()->get('connection'), 'projects');
        });
        $this->getContainer()->share('projects_validator', function () {
            return new \Dime\Server\Validator([
                'required' => new \Dime\Server\Validator\Required(['alias'])
            ]);
        });
        $this->getContainer()->share('services_repository', function() {
            return new \Dime\Server\Repository($this->getContainer()->get('connection'), 'services');
        });
        $this->getContainer()->share('services_validator', function () {
            return new \Dime\Server\Validator([
                'required' => new \Dime\Server\Validator\Required(['alias'])
            ]);
        });
        $this->getContainer()->share('settings_repository', function() {
            return new \Dime\Server\Repository($this->getContainer()->get('connection'), 'settings');
        });
        $this->getContainer()->share('settings_validator', function () {
            return new \Dime\Server\Validator([
                'required' => new \Dime\Server\Validator\Required(['name', 'value'])
            ]);
        });
        $this->getContainer()->share('tags_repository', function() {
            return new \Dime\Server\Repository($this->getContainer()->get('connection'), 'tags');
        });
        $this->getContainer()->share('tags_validator', function () {
            return new \Dime\Server\Validator([
                'required' => new \Dime\Server\Validator\Required(['name'])
            ]);
        });
        $this->getContainer()->share('timeslices_repository', function() {
            return new \Dime\Api\Repository\Timeslices($this->getContainer()->get('connection'));
        });
        $this->getContainer()->share('timeslices_validator', function () {
            return new \Dime\Server\Validator([
                'required' => new \Dime\Server\Validator\Required(['activity_id'])
            ]);
        });
        $this->getContainer()->share('users_repository', function() {
            return new \Dime\Server\Repository($this->getContainer()->get('connection'), 'users');
        });

        $this->getContainer()->share('assignable', function() {
            return new \Dime\Server\Behavior\Assignable($this->getContainer()->get('session')->getUserId());
        });
    }
}
