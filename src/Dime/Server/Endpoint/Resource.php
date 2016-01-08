<?php

namespace Dime\Server\Endpoint;

use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class Resource
{
    protected $config;
    protected $manager;
    protected $repository;
    protected $serializer;

    public function __construct(array $config, EntityManager $manager)
    {
        $this->config = $config;
        $this->manager = $manager;
    }

    public function listAction(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $collection = $this->getRepository($args['resource'])->findAll();
        // TODO Pager
        // TODO filter
        return $this->render($request, $collection, $response);
    }

    public function getAction(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $entity = $this->getRepository($args['resource'])->find($args['id']);

        return $this->render($request, $entity, $response);
    }

    public function postAction(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
	
        return $this->render($request, $entity, $response);
    }

    public function putAction(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $entity = $this->getRepository($args['resource'])->find($args['id']);

        return $this->render($request, $entity, $response);
    }

    public function deleteAction(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $entity = $this->getRepository($args['resource'])->find($args['id']);

        if (!emtpy($entity)) {
            $this->manager->remove($entity);
            $this->manager->flush();
        } else {
          throw new NotFoundException('Resource not found'); 
        }

        return $this->render($request, $entity, $response);
    }

    protected function getRepository($resource)
    {
        if ($this->repository == null) {
            $this->repository = $this->manager->getRepository($this->config['resources'][$resource]['entity']);
        }
        return $this->repository;
    }
    
    protected function render(ServerRequestInterface $request, $content, ResponseInterface $response)
    {
        $serializer = $request->getAttribute("serializer");
        $response->getBody()->write($serializer($content));
        return $response;
    }
}
