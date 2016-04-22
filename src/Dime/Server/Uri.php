<?php

namespace Dime\Server;

use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouterInterface;
use Slim\Http\Environment;

class Uri
{

    private $router;
    private $env;

    public function __construct(RouterInterface $router, Environment $env)
    {
        $this->router = $router;
        $this->env = $env;
    }

    public function pathFor($name, array $data = [], array $queryParams = [])
    {
        return $this->env['CONTEXT_PREFIX'] . $this->router->pathFor($name, $data, $queryParams);
    }

    public function hasQueryParam(ServerRequestInterface $request, $name)
    {
        $parameters = $request->getQueryParams();
        return !empty($parameters) && isset($parameters[$name]);
    }

    public function getQueryParam(ServerRequestInterface $request, $name, $default = null)
    {
        $result = $default;
        if ($this->hasQueryParam($request, $name)) {
            $parameters = $request->getQueryParams();
            $result = $parameters[$name];
        }
        return $result;
    }

}
