<?php

namespace Dime\Server\Endpoint;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Dime\Server\Traits\ResourceTrait;
use Slim\Exception\NotFoundException;

class ResourcePut
{
    use ResourceTrait;
    
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $entity = $this->getRepository($args['resource'])->find($args['id']);

        if (empty($entity)) {
            throw new NotFoundException($request, $response);
        }

        $updateEntity = $request->getParsedBody();
        $updateEntity->setId($entity->getId());
        if ($updateEntity instanceof \Dime\Server\Behaviors\Assignable) {
            $updateEntity->setUserId(1); // $request->getAttribute("userId");
        }

        var_dump($updateEntity);

        $this->getManager()->persist($updateEntity);
        $this->getManager()->flush();
        $this->getManager()->refresh($entity);

        return $this->createResponse($response, $entity);
    }
}
