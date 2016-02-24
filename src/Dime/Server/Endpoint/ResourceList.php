<?php

namespace Dime\Server\Endpoint;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Dime\Server\Traits\ResourceTrait;
use Dime\Server\Helper\UriHelper;

class ResourceList
{
    use ResourceTrait;
    
    protected $uriHelper;

    public function __construct(array $config, EntityManager $manager, UriHelper $uriHelper)
    {
        $this->config = $config;
        $this->manager = $manager;
        $this->uriHelper = $uriHelper;
    }
    
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $repository = $this->getRepository($args['resource']);
        $repository->scopeByField('userId', 1); // $request->getAttribute("userId");

        if ($this->hasQueryParam($request, 'filter')) {
            $repository->filter($this->getQueryParam($request, 'filter'));
        }

        $page = $this->getQueryParam($request, 'page', 1);
        $with =  $this->getQueryParam($request, 'with', $this->getResourceConfig($args['resource'], 'pageSize', -1));

        $queryBuilder = $repository->getQueryBuilder();
        $queryBuilder->setFirstResult($with * ($page - 1));
        if ($with > 0) {
            $queryBuilder->setMaxResults($with);
        }

        $paginator = new Paginator($queryBuilder, true);
        $total = $paginator->count();
        return $this->createResponse(
            $response
                ->withHeader("X-Dime-Total", $total)
                ->withHeader('Link', $this->buildLink($request, $args['resource'], $page, $with, $total)),
            $paginator->getQuery()->getResult()
        );
    }

    protected function buildLink(ServerRequestInterface $request, $resource, $page, $with, $total)
    {
        $lastPage = ceil($total / $with);
        $queryParameter = $request->getQueryParams();
        $queryParameter['with'] = $with;
        $result = [];
        if ($page + 1 <= $lastPage) {
            $queryParameter['page'] =  $page + 1;
            $result[] = sprintf('<%s>; rel="next"', $this->uriHelper->pathFor(
                'resource_list',
                ['resource' => $resource],
                $queryParameter
            ));
        }

        if ($page - 1 > 0) {
            $queryParameter['page'] =  $page - 1;
            $result[] = sprintf('<%s>; rel="prev"', $this->uriHelper->pathFor(
                'resource_list',
                ['resource' => $resource],
                $queryParameter
            ));
        }

        if ($page != $lastPage) {
            $queryParameter['page'] =  1;
                $result[] = sprintf('<%s>; rel="first"', $this->uriHelper->pathFor(
                    'resource_list',
                    ['resource' => $resource],
                    $queryParameter
                ));

            $queryParameter['page'] =  $lastPage;
            $result[] = sprintf('<%s>; rel="last"', $this->uriHelper->pathFor(
                'resource_list',
                ['resource' => $resource],
                $queryParameter
            ));
        }

        return implode(', ', $result);
    }
}
