<?php

namespace Dime\Server\Endpoint;

use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\NotFoundException;

class ResourcePut
{
    use \Dime\Server\Traits\ConfigurationTrait;
    use \Dime\Server\Traits\ManagerTrait;
    use \Dime\Server\Traits\ResponseTrait;

    public function __construct(array $config, EntityManager $manager)
    {
        $this->setConfig($config);
        $this->setManager($manager);
    }
    
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $repositoryName = $this->getConfigValue(['resources', $args['resource'], 'entity']);

        $entity = $this->getRepository($repositoryName)->find($args['id']);

        if (empty($entity)) {
            throw new NotFoundException($request, $response);
        }

        $updateEntity = $request->getParsedBody();
        var_dump($entity, $updateEntity); die();

        if ($updateEntity->getId() !== $entity->getId()) {
            throw new NotFoundException($request, $response);
        }

        if ($updateEntity instanceof \Dime\Api\Entity\Timeslice) {
            $updateEntity->setActivity($entity->getActivity());
        }

        return $this->createResponse($response, $this->save($updateEntity));
    }
}
