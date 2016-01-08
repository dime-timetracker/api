<?php

namespace Dime\Server\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\NotFoundException;

class ResourceType implements Middleware
{
    /**
     * @var array
     */
    protected $config = [];
 
    public function __construct(array $config)
    {
        $this->config = $config;
    }
    
    public function run(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $resource = $request->getAttribute('route')->getArgument('resource');
        
        if (!isset($this->config['resources'][$resource])) {
            throw new NotFoundException($request, $response);
        }
                
        return $next($request, $response);
    }

}
