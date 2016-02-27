<?php

namespace Dime\Server\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class AuthorizeType implements Middleware
{
    use \Dime\Server\Traits\ConfigurationTrait;

    public function __construct(array $config)
    {
        $this->setConfig($config);
    }

    public function run(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        return $next(
            $request->withAttribute('type', $this->getConfigValue(['authorizeType'])),
            $response
        );
    }
}
