<?php

namespace Dime\Server\Endpoint;

use Dime\Server\Entity\ResourceRepository;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\NotFoundException;

class ResourceGet
{
    use \Dime\Server\Traits\EndpointTrait;
    
    /**
     * @var ResourceRepository
     */
    private $repository;
    
    public function __construct(ResourceRepository $repository)
    {
        $this->repository = $repository;
    }
    
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->repository->setName(filter_var($args['resource'], FILTER_SANITIZE_STRING));
        $identifier = ['id' => filter_var($args['id'], FILTER_SANITIZE_NUMBER_INT)];
        
        // Select
        $result = $this->repository->find($identifier);
        if ($result === FALSE) {
            throw new NotFoundException($request, $response);
        }
        
        return $this->respond($response, $data);
    }
}
