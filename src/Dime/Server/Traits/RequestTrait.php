<?php

namespace Dime\Server\Traits;

use Psr\Http\Message\ServerRequestInterface;

trait RequestTrait
{
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