<?php

namespace Dime\Server\Endpoint;

use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\NotFoundException;
use Dime\Server\Entity\Access;
use Dime\Server\Entity\UserInterface;
use Dime\Server\Hash\Hasher;

/**
 * Authentication is a endpoint with to functions. It will login and logout a 
 * user.
 */
class Authentication
{
    use \Dime\Server\Traits\ConfigurationTrait;
    use \Dime\Server\Traits\DoctrineTrait;
    use \Dime\Server\Traits\ResponseTrait;

    protected $hasher;

    public function __construct(array $config, EntityManager $manager, Hasher $hasher)
    {
        $this->setConfig($config);
        $this->setManager($manager);
        $this->hasher = $hasher;
    }

    public function login(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $login = $request->getParsedBody();

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
        
        return $this->createResponse($response, [
            'token' => $access->getToken(),
            'expires' => $access->expires($this->getConfigValue(['expires']))
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

    protected function getAccessRepository()
    {
        return $this->getRepository($this->getConfigValue(['access']));
    }

    protected function getUserRepository()
    {
        return $this->getRepository($this->getConfigValue(['user']));
    }
}
