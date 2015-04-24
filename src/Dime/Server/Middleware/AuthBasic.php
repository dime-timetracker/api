<?php

namespace Dime\Server\Middleware;

use Slim\Middleware;

/**
 * Digest
 * 
 * Copy of https://github.com/codeguy/Slim-Extras/blob/master/Middleware/HttpDigestAuth.php
 * 
 * Use this middleware with your Slim Framework application
 * to require HTTP digest auth for all routes.
 *
 * Much of this code was created using <http://php.net/manual/en/features.http-auth.php>
 * as a reference. I do not claim ownership or copyright on this code. This
 * derivative class is provided under the MIT public license.
 *
 * @author Josh Lockhart <info@slimframework.com>
 * @author Samer Bechara <sam@thoughtengineer.com>
 * @version 1.0
 *
 * USAGE
 *
 * $app = new \Slim\Slim();
 * $app->add(new \Slim\Extras\Middleware\HttpDigestAuth(array('user1' => 'password1', 'user2' => 'password2')));
 *
 * MIT LICENSE
 */
class AuthBasic extends Middleware
{

    /**
     * Constructor
     *
     * @param   string	$config
     * @return  void
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * Call
     *
     * This method will check the HTTP request headers for previous authentication. If
     * the request has already authenticated, the next middleware is called. Otherwise,
     * a 401 Authentication Required response is returned to the client.
     *
     * @return void
     */
    public function call()
    {
        $this->app->log->debug('Call middleware [AuthBasic]');

        $username = $this->app->request()->headers('PHP_AUTH_USER');
        $password = $this->app->request()->headers('PHP_AUTH_PW');

        if (!empty($username)  && !empty($password)) {
            $modelClass = $this->config['model'];
            $hasher = new \Dime\Server\Hash\SymfonySecurityHasher();
            $user = $modelClass::where('username', '=', $username)->firstOrFail();
            if (!empty($user) && $hasher->check($password, $user->password, ['salt' => $user->salt])) {
                $this->next->call();
            } else {
                $this->fail();
            }
        } else {
            $this->fail();
        }
    }

    /**
     * Require Authentication from HTTP Client
     *
     * @return void
     */
    protected function fail()
    {
        $this->app->response()->status(401);
        $this->app->response()->header('WWW-Authenticate', sprintf('Basic realm="%s"', $this->config['realm']));
    }

}
