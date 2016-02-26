<?php

namespace Dime\Server\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class AuthorizeType implements Middleware
{
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function run(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        return $next(
            $request->withAttribute('type', $this->config['authorizeType']),
            $response
        );
    }
}
