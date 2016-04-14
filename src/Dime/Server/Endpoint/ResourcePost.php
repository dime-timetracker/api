<?php

namespace Dime\Server\Endpoint;

use Doctrine\DBAL\Connection;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\NotFoundException;

class ResourcePost
{

    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
    
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $resource = filter_var($args['resource'], FILTER_SANITIZE_STRING);
        
        $updateData = $request->getParsedBody();
        
        // Validate
        
        // Timestample
        $updateData['created_at'] = date('Y-m-d H:i:s');
        $updateData['updated_at'] = date('Y-m-d H:i:s');
        
        // Assign
        $updateData['user_id'] = 1;

        
        $qb = $this->connection->createQueryBuilder()->insert($resource);
        $qb->values($updateData);
        var_dump($qb->execute());
        
                
        $response->getBody()->write(
            json_encode(
                $updateData, 
                JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE
            )
        );
        
        return $response;
    }
}
