<?php

namespace Dime\Api\Action;

use League\Container\ContainerInterface;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\NotFoundException;
use Slim\Exception\SlimException;

class RegisterAction implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $parsedData = $request->getParsedBody();

        if (empty($parsedData['username'])) {
            throw new \Exception('No data');
        }

        $repository = $this->get('users_repository');
        $user = $repository->find([ 'username' => $parsedData['username'] ]);
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
    }
}
