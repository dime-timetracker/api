<?php

namespace Dime\Api\Action;

use League\Container\ContainerInterface;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\NotFoundException;
use Slim\Exception\SlimException;

class LogoutAction implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $username = $request->getAttribute('username');
        $client = $request->getAttribute('client');

        if (empty($username) || empty($client)) {
            throw new NotFoundException($request, $response);
        }

        $user = $this->get('users_repository')->find([ 'username' => $username ]);
        if (!empty($user)) {
            $this->get('access_repository')->delete([
                'user_id' => $user['id'],
                'client' => $client
            ]);
        }

        return $response;
    }
}
