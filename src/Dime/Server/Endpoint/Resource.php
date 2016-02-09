<?php

namespace Dime\Server\Endpoint;

use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\NotFoundException;
use Dime\Server\Exception\NotValidException;

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
        $parameter = $request->getQueryParams();
       
        $repository = $this->getRepository($args['resource']);
        if (isset($parameter['filter']) && $repository instanceof Filterable) {
            $collection = $repository->filter($parameter['filter']);
        } else {
            $collection = $repository->findAll();
        }
        // TODO Pager
        // TODO filter
        return $this->render($request, $collection, $response);
    }

    public function getAction(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $entity = $this->getRepository($args['resource'])->find($args['id']);
        if (empty($entity)) {
            throw new NotFoundException($request, $response);
        }

        return $this->render($request, $entity, $response);
    }

    public function postAction(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $entity = $request->getParsedBody();
        
        if (empty($entity)) {
            throw new NotValidException($request, $response);
        }
        
        // TODO createdAt / updatedAt
       // $this->manager->persist($entity);
       // $this->manager->flush();
        $response = $this->render($request, $entity, $response);
        
        return $response;
    }

    public function putAction(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $entity = $this->getRepository($args['resource'])->find($args['id']);
        if (emtpy($entity)) {
            throw new NotFoundException($request, $response); 
        }
        
        // TODO merge with entity

        return $this->render($request, $entity, $response);
    }

    public function deleteAction(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $entity = $this->getRepository($args['resource'])->find($args['id']);

        if (emtpy($entity)) {
            throw new NotFoundException($request, $response); 
        }
        
        $this->manager->remove($entity);
        $this->manager->flush();

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
