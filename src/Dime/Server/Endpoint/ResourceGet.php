<?php

namespace Dime\Server\Endpoint;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Dime\Server\Traits\ResourceTrait;
use Slim\Exception\NotFoundException;

class ResourceGet
{
    use ResourceTrait;

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        try {
            $entity = $this->getRepository($args['resource'])
                ->scopeByField('userId', 1)
                ->scopeByField('id', $args['id'])
                ->getQueryBuilder()
                ->getQuery()->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $ex) {
            $entity = null;
        }

        if (empty($entity)) {
            throw new NotFoundException($request, $response);
        }

        return $this->createResponse($response, $entity);
    }

}
