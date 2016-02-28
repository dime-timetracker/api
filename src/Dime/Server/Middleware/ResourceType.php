<?php

namespace Dime\Server\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\NotFoundException;

class ResourceType implements MiddlewareInterface
{
    use \Dime\Server\Traits\ConfigurationTrait;
 
    public function __construct(array $config)
    {
        $this->setConfig($config);
    }
    
    public function run(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $resource = $request->getAttribute('route')->getArgument('resource');
        $type = $this->getConfigValue(['resources', $resource, 'entity']);

        if (empty($type)) {
            throw new NotFoundException($request, $response);
        }

        return $next($request->withAttribute('type', $type), $response);
    }

}
