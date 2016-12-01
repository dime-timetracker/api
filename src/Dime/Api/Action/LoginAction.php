<?php

namespace Dime\Api\Action;

use League\Container\ContainerInterface;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\NotFoundException;
use Slim\Exception\SlimException;

class LoginAction implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $login = $request->getParsedBody();

        $user = $this->getContainer()->get('users_repository')->find(['username' => $login['username']]);
        if (!$this->getContainer()->get('security')->authenticate($user, $login['password'])) {
            return $this->getContainer()->get('responder')->respond($response, ['message' => 'Bad password.'], 401);
        }

        $identifier = [ 'user_id' => $user['id'], 'client' => $login['client'] ];

        $access = $this->getContainer()->get('access_repository')->find($identifier);
        if (empty($access)) {
            $access = $identifier;
            $access['token'] = $this->getContainer()->get('security')->createToken($user['id'], $login['client']);

            $access = \Dime\Server\Stream::of($access)
                    ->append(new \Dime\Server\Behavior\Timestampable())
                    ->collect();

            $this->getContainer()->get('access_repository')->insert($access);
        } else {
            $access['token'] = $this->getContainer()->get('security')->createToken($user['id'], $login['client']);

            $access = \Dime\Server\Stream::of($access)
                    ->append(new \Dime\Server\Behavior\Timestampable(null))
                    ->collect();

            $this->getContainer()->get('access_repository')->update($access, $identifier);
        }

        return $this->getContainer()->get('responder')->respond($response, [
            'token' => $access['token'],
            'expires' => $this->getContainer()->get('security')->expires($access['updated_at'])
        ]);
    }
}
