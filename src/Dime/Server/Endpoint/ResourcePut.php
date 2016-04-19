<?php

namespace Dime\Server\Endpoint;

use Dime\Server\Entity\ResourceRepository;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\NotFoundException;

class ResourcePut
{

    /**
     * @var ResourceRepository
     */
    private $repository;
    
    private $behavior;

    public function __construct(ResourceRepository $repository)
    {
        $this->repository = $repository;
        
        $this->behavior = new \Dime\Server\Behavior\Behavior();
        $this->behavior->add(new \Dime\Server\Behavior\Timestampable(null));
        $this->behavior->add(new \Dime\Server\Behavior\Assignable());
    }
    
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->repository->setName(filter_var($args['resource'], FILTER_SANITIZE_STRING));
        $identifier = [
            'id' => filter_var($args['id'], FILTER_SANITIZE_NUMBER_INT)
        ];
        
        $result = $this->repository->find($identifier);
        if ($result === FALSE) {
            throw new NotFoundException($request, $response);
        }
        
        $parsedData = $request->getParsedBody();        
        if (empty($parsedData)) {
            throw new \Exception("No data");
        }
        
        // Behave
        $behavedData = $this->behavior->execute($parsedData);
        
        // Validate
        $validator = new \Dime\Server\Validator\Validator();
        if (!$validator->validate($behavedData)) {
            return $this->respond($response, $validator->getErrors(), 400);
        }
        
        try {
            $this->repository->update($behavedData, $identifier);
        } catch (\Exception $e) {
            var_dump($e);
        }
        
        return $this->respond($response, $this->repository->find($identifier));
    }
    
    protected function respond(ResponseInterface $response, array $data, $status = 200)
    {
        if ($status !== 200) {
            $response = $response->withStatus($status);
        }
        
        $response->getBody()->write(
            json_encode(
                $data, 
                JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE
            )
        );
        return $response;
    }
}
