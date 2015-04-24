<?php

namespace Dime\Server\Middleware;

use Slim\Middleware;

/**
 * Before
 *
 * @author Danilo Kuehn <dk@nogo-software.de>
 */
class Route extends Middleware
{
    protected $route;
    
    /**
     * @var Middleware
     */
    protected $middleware;
    
    public function __construct($route, Middleware $middleware)
    {
        $this->route = $route;
        $this->middleware = $middleware;
    }
    
    public function call()
    {
        if (strpos($this->app->request()->getPathInfo(), $this->route) !== false) {
            $this->middleware->setApplication($this->app);
            $this->middleware->setNextMiddleware($this->next);
            $this->middleware->call();
        } else {
            $this->next->call();
        }
    }
}
