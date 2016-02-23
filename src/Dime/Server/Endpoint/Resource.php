<?php

namespace Dime\Server\Endpoint;

use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class Resource
{
    use \Dime\Server\Traits\DimeResponseTrait;
    
    protected $config;
    protected $manager;
    protected $repository;

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
        // TODO only user entities
        // TODO Pager
        // TODO filter

        return $this->createResponse($response, $collection);
    }

    public function getAction(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $entity = $this->getRepository($args['resource'])->find($args['id']);

        // TODO only user entities
        
        if (empty($entity)) {
            throw new NotFoundException($request, $response);
        }

        return $this->createResponse($response, $entity);
    }

    public function postAction(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $entity = $request->getParsedBody();

        if ($entity instanceof \Dime\Server\Behaviors\Assignable) {
            $entity->setUserId(1);
//        $entity->setUserId($request->getAttribute("userId"));
        }

        if ($entity instanceof \Dime\Server\Behaviors\Timestampable) {
            $entity->setCreatedAt(new \DateTime());
            $entity->setUpdatedAt(new \DateTime());
        }
        
        $this->getManager()->persist($entity);
        $this->getManager()->flush();

        return $this->createResponse($response, $entity);
    }

    public function putAction(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $entity = $this->getRepository($args['resource'])->find($args['id']);

        if (empty($entity)) {
            throw new NotFoundException($request, $response); 
        }

        $updateEntity = $request->getParsedBody();

        $updateEntity->setId($entity->getId());
        if ($updateEntity instanceof \Dime\Server\Behaviors\Assignable) {
            $updateEntity->setUserId(1);
//        $entity->setUserId($request->getAttribute("userId"));
        }

        if ($updateEntity instanceof \Dime\Server\Behaviors\Timestampable) {
            $updateEntity->setUpdatedAt(new \DateTime());
        }

        $this->getManager()->persist($entity);
        $this->getManager()->flush();
        
        return $this->createResponse($response, $entity);
    }

    public function deleteAction(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $entity = $this->getRepository($args['resource'])->find($args['id']);

        // TODO only user entities

        if (empty($entity)) {
            throw new NotFoundException($request, $response); 
        }
        
        $this->getManager()->remove($entity);
        $this->getManager()->flush();

        return $this->createResponse($response, $entity);
    }

    protected function getManager()
    {
        return $this->manager;
    }

    protected function getRepository($resource)
    {
        if ($this->repository == null) {
            $this->repository = $this->getManager()->getRepository($this->config['resources'][$resource]['entity']);
        }
        return $this->repository;
    }
}
