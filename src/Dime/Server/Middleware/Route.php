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
        if (!is_array($route)) {
            $this->route = [ $route ];
        } else {
            $this->route = $route;
        }
        $this->middleware = $middleware;
    }
    
    public function call()
    {
        if ($this->checkRoute($this->app->request()->getPathInfo()) !== false) {
            $this->middleware->setApplication($this->app);
            $this->middleware->setNextMiddleware($this->next);
            $this->middleware->call();
        } else {
            $this->next->call();
        }
    }
    
    public function checkRoute($path)
    {
        $result = false;
        foreach ($this->route as $part) {
            if (strpos($path, $part) !== false) {
                $result = true;
                break;
            }
        }
        return $result;
    }
}
