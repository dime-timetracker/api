<?php

namespace Dime\Server\Middleware;

use Dime\Server\Model\Access;
use Slim\Middleware;

/**
 * Auth
 *
 * Authorization: ALGORITHM USER,CLIENT-ID,TOKEN
 *
 * @author Danilo Kuehn <dk@nogo-software.de>
 */
class Authorization extends Middleware
{
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
        $authentication = $this->app->request()->headers('X-Authorization');
        if (!empty($authentication)) {
            $authentication = preg_split('/[\s,]/', $authentication);

            try {
                $access = Access::whereHas('user', function ($q) use ($authentication) {
                    $q->where('username', $authentication[1]);
                })->whereClientAndToken($authentication[2], $authentication[3])->firstOrFail();
                if (!empty($access)) {

                    // TODO Check ExpireDate

                    $this->app->access = $access;
                    $this->app->user = $access->user;
                    $this->next->call();
                } else {
                    $this->fail();
                }
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
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
        $this->app->response()->status(400);
    }
}
