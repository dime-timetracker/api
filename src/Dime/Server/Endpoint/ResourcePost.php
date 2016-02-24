<?php

namespace Dime\Server\Endpoint;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Dime\Server\Traits\ResourceTrait;
use Slim\Exception\NotFoundException;

class ResourcePost
{
    use ResourceTrait;
    
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $entity = $request->getParsedBody();

        if ($entity instanceof \Dime\Server\Behaviors\Assignable) {
            $entity->setUserId(1);
//        $entity->setUserId($request->getAttribute("userId"));
        }

        $this->getManager()->persist($entity);
        $this->getManager()->flush();
        $this->getManager()->refresh($entity);

        return $this->createResponse($response, $entity);
    }
}
