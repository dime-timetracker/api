<?php

namespace Dime\Server\Endpoint;

use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\NotFoundException;

class ResourceGet
{
    use \Dime\Server\Traits\ConfigurationTrait;
    use \Dime\Server\Traits\DoctrineTrait;
    use \Dime\Server\Traits\ResponseTrait;

    public function __construct(array $config, EntityManager $manager)
    {
        $this->setConfig($config);
        $this->setManager($manager);
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $repositoryName = $this->getConfigValue(['resources', $args['resource'], 'entity']);
        $userId = $request->getAttribute('userId', 1);
        $id = $args['id'];

        try {
            $entity = $this->getRepository($repositoryName)
                ->scopeByField('userId', $userId)
                ->scopeByField('id', $id)
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
