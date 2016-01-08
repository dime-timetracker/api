<?php

namespace Dime\Server\Endpoint;

use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\NotFoundException;
use Dime\Server\Entity\Access;
use Dime\Server\Entity\User;
use Dime\Server\Exception\NotAuthenticatedException;
use Dime\Server\Hash\Hasher;

/**
 * Authentication is a endpoint with to functions. It will login and logout a 
 * user.
 */
class Authentication
{
    protected $config;
    protected $manager;
    protected $repository;
    protected $hasher;

    public function __construct(array $config, EntityManager $manager, Hasher $hasher)
    {
        $this->config = $config;
        $this->manager = $manager;
        $this->hasher = $hasher;
    }

    public function login(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        // FIXME
        $input = filter_var_array($request->getBody(), [
            'username' => FILTER_SANITIZE_STRING,
            'client' => FILTER_SANITIZE_STRING,
            'password' => FILTER_SANITIZE_STRING
        ]);

        if (empty($input['username']) || empty($input['client']) || empty($input['password'])) {
            throw new NotAuthenticatedException();
        }
        
        $user = $this->manager
                    ->getRepository('Dime\Server\Entity\User')
                    ->findOneBy(['username' => $input['username']]);
        if (!$this->authenticate($user, $input['password'])) {
            throw new NotAuthenticatedException();
        }
        
        $access = new Access($user, $input['client']);
        $access->setToken($this->hasher->make(uniqid($input['username'] . $input['client'] . microtime(), true)));
        $this->manager->persist($access);
        $this->manager->flush();
        $response->withStatus(200);
        $response->content = [
            'token' => $access->getToken(),
            'expires' => $access->expires($this->config['expires'])
        ];
        
        return $response;
    }
    
    public function logout(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $username = $request->getAttribute('username');
        $client = $request->getAttribute('client');
        
        if (empty($username) || empty($client)) {
            throw new NotFoundException();
        }
        
        $user = $this->manager->getRepository('Dime\Server\Entity\User')
                    ->findOneBy(['username' => $username]);
        
        if ($user != null) {
            $access = $this->manager
                    ->getRepository('Dime\Server\Entity\Access')
                    ->findOneBy([
                        'user' => $user,
                        'client' => $client
                    ]);
            if ($access != null) {
                $this->manager->remove($access);
                $this->manager->flush();
            }
        }
        return $response;
    }

    /**
     * Authenticate user with password.
     * 
     * @param User $user
     * @param string $password
     * @return boolean
     */
    protected function authenticate(User $user, $password) {
        return !empty($user)
            && $this->hasher->check(
                    $password, 
                    $user->password, 
                    ['salt' => $user->salt]
            );
    }
}
