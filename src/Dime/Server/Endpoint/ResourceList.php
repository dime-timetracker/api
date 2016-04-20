<?php

namespace Dime\Server\Endpoint;

use Dime\Server\Entity\ResourceRepository;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

//use Dime\Server\Helper\UriHelper;

class ResourceList
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
        
        $result = $this->repository->findAll();
        
        return $this->respond($response, $result);
        
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
