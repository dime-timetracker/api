<?php

namespace Dime\Api\Action;

use League\Container\ContainerInterface;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\NotFoundException;
use Slim\Exception\SlimException;

class ListAction implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $repository = $this->getContainer()->get($args['resource'] . '_repository');
        $page = $this->getContainer()->get('uri')->getQueryParam($request, 'page', 1);
        $with = $this->getContainer()->get('uri')->getQueryParam($request, 'with',  100);
        $by = $this->getContainer()->get('uri')->getQueryParam($request, 'by', []);

        $filter = [];
        if ($this->getContainer()->has($args['resource'] . '_filter')) {
            $filter = $this->getContainer()->get($args['resource'] . '_filter')->build($by);
        }

        $scopes = array_merge($filter, [
            new \Dime\Server\Scope\WithScope(['user_id' => $this->getContainer()->get('session')->getUserId()]),
            new \Dime\Server\Scope\PaginationScope($page, $with)
        ]);

        try {
            $result = $repository->findAll($scopes);
        } catch (\Exception $ex) {
            if ($this->getContainer()->get('settings')['displayErrorDetails']) {
                $response->getBody()->write($ex->getMessage());
            }
            throw new SlimException($request, $response->withStatus(500));
        }

        // add header X-Dime-Total and Link
        $total = $repository->count($scopes);
        $link = $this->getContainer()->get('uri')->buildLinkHeader($request, $total, $page, $with);

        return $this->getContainer()->get('responder')->respond(
            $response
                ->withHeader("X-Dime-Total", $total)
                ->withHeader('Link', implode(', ', $link)),
            $result
        );
    }
}
