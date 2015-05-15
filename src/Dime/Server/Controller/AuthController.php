<?php

namespace Dime\Server\Controller;

use Dime\Server\Controller\SlimController;
use Dime\Server\Model\Access;
use Dime\Server\Model\User;
use Dime\Server\Hash\SymfonySecurityHasher as Hasher;
use Slim\Slim;

/**
 * AuthController
 *
 * @author Danilo Kuehn <dk@nogo-software.de>
 */
class AuthController implements SlimController
{

    /**
     * @var Slim
     */
    protected $app;

    /**
     * @var Hasher
     */
    protected $hasher;
    

    public function enable(Slim $app)
    {
        $this->app = $app;
        $this->hasher = new Hasher();
        
        $this->app->post('/login', [$this, 'loginAction']);

        $this->app->add(new Route('/logout', new AuthMiddleware($this->app->config('auth'))));
        $this->app->post('/logout', [$this, 'logoutAction']);
    }

    /**
     * Create an access token and send it back.
     *
     * [POST] /login
     *
     * {
     *   username: USERNAME
     *   client: CLIENTID
     *   password: PASSWORD
     * }
     */
    public function loginAction()
    {
        $username = filter_var($this->app->request->post('username'), FILTER_SANITIZE_STRING);
        $client   = filter_var($this->app->request->post('client'), FILTER_SANITIZE_STRING);
        $password = filter_var($this->app->request->post('password'), FILTER_SANITIZE_STRING);

        $user = User::where('username', '=', $username)->firstOrFail();
        if ($this->authenticate($user, $password)) {
            // TODO generate Token
            $access = Access::create();
            $access->user()->associate($user);
            $access->clientId = $client;
            $access->token =  $this->hasher->make(uniqid($username . $client . microtime(), true));
            $access->save();
        }
    }

    /**
     * Remove access token.
     *
     * [POST] /logout
     *
     * 
     */
    public function logoutAction()
    {
        if ($this->app->access) {
            $this->app->access->delete();
        }
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
            && $this->hasher->check($password, $user->password, ['salt' => $user->salt]);
    }
}
