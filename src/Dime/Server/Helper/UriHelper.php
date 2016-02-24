<?php

namespace Dime\Server\Helper;

use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouterInterface;
use Slim\Http\Environment;

class UriHelper
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
}
