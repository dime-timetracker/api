<?php

namespace Dime\Security\Middleware;

use Dime\Security\Behaviors\Assignable;
use Dime\Server\Middleware\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class Assign implements MiddlewareInterface
{
    public function run(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $entity = $request->getParsedBody();
        if (!empty($entity) && $entity instanceof Assignable) {
            $entity->setUserId($request->getAttribute('userId', 1));
        }

        return $next($request->withParsedBody($entity), $response);
    }
}
