<?php

namespace Dime\Server\Middleware;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Slim\Middleware;

/**
 * Authorization is a middleware and read the HTTP header Authorization or X-Authorization.
 *
 * Tasks:
 * - MUST check realm configuration
 * - MUST check the username, client, token exists in storage
 * - MUST check the updated_at with the configured expire period
 * - MUST delete token when expired
 *
 * Header:
 * Authorization: REALM USER,CLIENT,TOKEN
 *
 * or
 *
 * X-Authorization: REALM USER,CLIENT,TOKEN
 *
 * @author Danilo Kuehn <dk@nogo-software.de>
 */
class Authorization extends Middleware
{

    /**
     * @var array
     */
    protected $config;

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

    public function call()
    {
        $next = false;
        $authorization = $this->headerAuthorization();
        if (!empty($authorization) && $authorization[0] === $this->config['realm']) {
            try {
                $accessClass = $this->config['access'];
                $access = $accessClass::whereHas('user', function ($q) use ($authorization) {
                            $q->where('username', $authorization[1]);
                        })->whereClientAndToken($authorization[2], $authorization[3])->firstOrFail();
                if (!$access->expired($this->config['expires'])) {
                    $this->app->access = $access;
                    $this->app->user = $access->user;
                    $next = true;
                } else {
                    $access->delete();
                }
            } catch (ModelNotFoundException $ex) {
                $this->app->error($ex);
            }
        }

        if ($next) {
            $this->next->call();
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
        $this->app->response()->body(json_encode(['error' => 'Authentication error']));
    }

    /**
     * Read Authorization / X-Authorization header and split it into an array;
     * @return mixed array or false
     */
    protected function headerAuthorization()
    {
        $authorization = false;
        $headers = $this->app->request()->headers();
        if (!isset($headers['Authorization']) && function_exists('apache_request_headers')) {
            $all = apache_request_headers();
            if (isset($all['Authorization'])) {
                $authorization = $all['Authorization'];
            }
        } else {
            $authorization = $headers['X-Authorization'];
        }

        return (!empty($authorization)) ? preg_split('/[\s,]/', $authorization) : false;
    }

}
