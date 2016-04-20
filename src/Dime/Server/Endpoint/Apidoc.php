<?php

namespace Dime\Server\Endpoint;

use Dime\Server\Metadata\Metadata;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\NotFoundException;

class Apidoc
{
    use \Dime\Server\Traits\EndpointTrait;
    
    private $metadata;
    
    public function __construct(Metadata $metadata)
    {
        $this->metadata = $metadata;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        if (isset($args['resource'])) {
            $resource = filter_var($args['resource'], FILTER_SANITIZE_STRING);
        }
         
        // TODO add resource routes
        // TODO Filter some details
        
        if (empty($resource)) {
            $result = $this->metadata->resources();
        } else {
            if (!$this->metadata->hasResource($resource)) {
                throw new NotFoundException($request, $response);
            }
            
            $result = [];
            $details = $this->metadata->resource($resource);
            foreach ($details->getColumns() as $key => $detail) {
                $result[$key] = strtolower($detail->getType()->__toString());
            }
        }
                
        return $this->respond($response, $result);
    }
}
