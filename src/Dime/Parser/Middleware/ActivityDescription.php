<?php

namespace Dime\Parser\Middleware;

use Dime\Server\Middleware\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class ActivityDescription implements MiddlewareInterface
{
    protected $regex = '/([@:\/])(\w+)/';
    protected $matches = array();

    public function run(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        return $next($request, $response);
    }

    public function clean($input)
    {
        return '';
    }

    public function run($input)
    {
        $this->result['description'] = $input;

        return $this->result;
    }

}
