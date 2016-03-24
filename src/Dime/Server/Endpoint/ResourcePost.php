<?php

namespace Dime\Server\Endpoint;

use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class ResourcePost
{
    use \Dime\Server\Traits\ManagerTrait;
    use \Dime\Server\Traits\ResponseTrait;

    public function __construct(EntityManager $manager)
    {
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
