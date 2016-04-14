<?php

namespace Dime\Server\Endpoint;

use Doctrine\DBAL\Connection;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\NotFoundException;

class ResourcePut
{

    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
    
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $resource = filter_var($args['resource'], FILTER_SANITIZE_STRING);
        $id = filter_var($args['id'], FILTER_SANITIZE_NUMBER_INT);
        
        // Query
        $qb = $this->connection->createQueryBuilder()->select("*")->from($resource);
        $qb->where($qb->expr()->eq('id', ':id'))->setParameter('id', $id);
        $result = $qb->execute()->fetch();
                
        if ($result === FALSE) {
            throw new NotFoundException($request, $response);
        }
        
        $updateData = $request->getParsedBody();
                
        $response->getBody()->write(
            json_encode(
                $result, 
                JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE
            )
        );
        
        return $response;
    }
}
