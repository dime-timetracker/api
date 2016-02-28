<?php

namespace Dime\Server\Endpoint;

use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class ResourcePost
{
    use \Dime\Server\Traits\DoctrineTrait;
    use \Dime\Server\Traits\ResponseTrait;

    public function __construct(array $config, EntityManager $manager)
    {
        $this->setConfig($config);
        $this->setManager($manager);
    }
    
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        return $this->createResponse(
            $response,
            $this->save($request->getParsedBody())
        );
    }
}
