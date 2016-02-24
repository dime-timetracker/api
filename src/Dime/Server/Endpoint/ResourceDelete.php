<?php

namespace Dime\Server\Endpoint;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Dime\Server\Traits\ResourceTrait;

class ResourceDelete
{
    use ResourceTrait;
    
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $entity = $this->getRepository($args['resource'])->find($args['id']);

        // TODO only user entities

        if (empty($entity)) {
            throw new NotFoundException($request, $response);
        }

        $this->getManager()->remove($entity);
        $this->getManager()->flush();

        return $this->createResponse($response, $entity);
    }

}
