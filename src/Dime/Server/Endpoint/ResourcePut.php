<?php

namespace Dime\Server\Endpoint;

use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\NotFoundException;
use Dime\Server\Behaviors\Assignable;

class ResourcePut
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

        $entity = $this->getRepository($repositoryName)->find($args['id']);

        if (empty($entity)) {
            throw new NotFoundException($request, $response);
        }

        // TODO check update Id with args[id]
        $updateEntity = $request->getParsedBody();
        if ($updateEntity instanceof Assignable) {
            $updateEntity->setUserId($request->getAttribute('userId', 1));
        }

        return $this->createResponse($response, $this->save($updateEntity));
    }
}
