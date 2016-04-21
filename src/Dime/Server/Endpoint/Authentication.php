<?php

namespace Dime\Server\Endpoint;

use Doctrine\DBAL\Connection;
use Dime\Security\Hash\Hasher;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\NotFoundException;

class Authentication
{
    use \Dime\Server\Traits\EndpointTrait;
    
    /**
     * @var ResourceRepository
     */
    private $connection;
    private $hasher;

    public function __construct(Connection $connection, Hasher $hasher)
    {
        $this->connection = $connection;
        $this->hasher = $hasher;
    }
    
    public function login(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $login = $request->getParsedBody();

        // Check data
        // Authenticate user
        // Generate token
        
        $user = $this->getUserRepository()->findOneBy(['username' => $login->getUser()]);
        if (!$this->authenticate($user, $login->getPassword())) {
            return $this->createResponse($response, ['message' => 'Bad password.'], 401);
        }

        $access = $this->getAccessRepository()->findOneBy([
            'userId' => $user->getId(),
            'client' => $login->getClient()
        ]);
        if (empty($access)) {
            $access = new Access($user->getId(), $login->getClient());
        }

        $access->setToken($access->generateToken($this->hasher));
        $this->save($access);
        
        return $this->respond($response, [
            'token' => '',
            'expires' => ''
        ]);
    }
    
    public function logout(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $username = $request->getAttribute('username');
        $client = $request->getAttribute('client');
        
        if (empty($username) || empty($client)) {
            throw new NotFoundException();
        }
        
        $user = $this->getUserRepository()->findOneBy(['username' => $username]);
        
        if ($user != null) {
            $access = $this->getAccessRepository()
                    ->findOneBy([
                        'userId' => $user->getId(),
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
     * @param UserInterface $user
     * @param string $password
     * @return boolean
     */
    protected function authenticate(UserInterface $user, $password) {
        return !empty($user)
            && $this->hasher->check(
                    $password, 
                    $user->getPassword(),
                    ['salt' => $user->getSalt()]
            );
    }
}
