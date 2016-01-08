<?php

namespace Dime\Server\Endpoint;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class Parser
{
    public function __construct()
    {
    }

    public function analyse(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        // TODO Analye request string statement
        // TODO Create entities and persit them ?
        // TODO Send back entity
        //
        return $response;
    }
}
