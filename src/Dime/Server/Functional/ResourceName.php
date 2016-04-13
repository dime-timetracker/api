<?php

namespace Dime\Server\Functional;

use Psr\Http\Message\ServerRequestInterface;

function resourceName(ServerRequestInterface $request)
{
    return $request->getAttribute('route')->getArgument('resource');
}