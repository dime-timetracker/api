<?php

namespace Dime\Server\Endpoint;

use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class ResourceDelete
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
        
        $entity = $this->getRepository($repositoryName)
                ->findOneBy([
                    'userId' => $request->getAttribute('userId', 1),
                    'id' => $args['id']
                ]);

        if (empty($entity)) {
            throw new NotFoundException($request, $response);
        }

        $this->getManager()->remove($entity);
        $this->getManager()->flush();

        return $this->createResponse($response, $entity);
    }

}
