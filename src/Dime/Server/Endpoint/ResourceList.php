<?php

namespace Dime\Server\Endpoint;

use Doctrine\DBAL\Connection;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

//use Dime\Server\Behaviors\Filterable;
//use Dime\Server\Helper\UriHelper;

class ResourceList
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
    
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $resource = filter_var($args['resource'], FILTER_SANITIZE_STRING);
        
        $qb = $this->connection->createQueryBuilder();
        $sql = $qb->select("*")->from($resource)->getSQL();
        
        $response->getBody()->write(
            json_encode(
                $this->connection->executeQuery($sql)->fetchAll(), 
                JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE
            )
        );
        
        return $response;
        
//        $repositoryName = $this->getConfigValue(['resources', $args['resource'], 'entity']);
//        $pageSize = $this->getConfigValue(['resources', $args['resource'], 'pageSize'], -1);
//        $userId = $request->getAttribute('id', 1);
//        $page = $this->getQueryParam($request, 'page', 1);
//        $with = $this->getQueryParam($request, 'with', $pageSize);
//
//        $repository = $this
//                ->getRepository($repositoryName)
//                ->scopeByField('userId', $userId);
//        if ($this->hasQueryParam($request, 'filter') && $repository instanceof Filterable) {
//            $repository
//                ->filter($this->getQueryParam($request, 'filter'));
//        }
//
//        $queryBuilder = $repository->getQueryBuilder();
//        $queryBuilder->setFirstResult($with * ($page - 1));
//        if ($with > 0) {
//            $queryBuilder->setMaxResults($with);
//        }
//
//        $paginator = new Paginator($queryBuilder, true);
//        $total = $paginator->count();
//        return $this->createResponse(
//            $response
//                ->withHeader("X-Dime-Total", $total)
//                ->withHeader('Link', $this->buildLink($request, $args['resource'], $page, $with, $total)),
//            $paginator->getQuery()->getResult()
//        );
    }

//    protected function buildLink(ServerRequestInterface $request, $resource, $page, $with, $total)
//    {
//        $lastPage = 1;
//        $queryParameter = $request->getQueryParams();
//        if ($with > 1) {
//            $lastPage = ceil($total / $with);
//            $queryParameter['with'] = $with;
//        }
//        $result = [];
//        if ($page + 1 <= $lastPage) {
//            $queryParameter['page'] =  $page + 1;
//            $result[] = sprintf('<%s>; rel="next"', $this->uriHelper->pathFor(
//                'resource_list',
//                ['resource' => $resource],
//                $queryParameter
//            ));
//        }
//
//        if ($page - 1 > 0) {
//            $queryParameter['page'] =  $page - 1;
//            $result[] = sprintf('<%s>; rel="prev"', $this->uriHelper->pathFor(
//                'resource_list',
//                ['resource' => $resource],
//                $queryParameter
//            ));
//        }
//
//        if ($page != $lastPage) {
//            $queryParameter['page'] =  1;
//                $result[] = sprintf('<%s>; rel="first"', $this->uriHelper->pathFor(
//                    'resource_list',
//                    ['resource' => $resource],
//                    $queryParameter
//                ));
//
//            $queryParameter['page'] =  $lastPage;
//            $result[] = sprintf('<%s>; rel="last"', $this->uriHelper->pathFor(
//                'resource_list',
//                ['resource' => $resource],
//                $queryParameter
//            ));
//        }
//
//        return implode(', ', $result);
//    }
}
