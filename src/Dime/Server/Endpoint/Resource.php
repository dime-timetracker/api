<?php

namespace Dime\Server\Endpoint;

use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Headers;
use Slim\Exception\NotFoundException;
use Dime\Server\Exception\NotValidException;
use Dime\Server\Http\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Resource
{
    protected $config;
    protected $manager;
    protected $validator;
    protected $repository;

    public function __construct(array $config, EntityManager $manager, ValidatorInterface $validator)
    {
        $this->config = $config;
        $this->manager = $manager;
        $this->validator = $validator;
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

        return $this->createResponse($response, $collection);
    }

    public function getAction(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $entity = $this->getRepository($args['resource'])->find($args['id']);
        if (empty($entity)) {
            throw new NotFoundException($request, $response);
        }

        return $this->createResponse($response, $entity);
    }

    public function postAction(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $entity = $request->getParsedBody();
        
        if (empty($entity)) {
            throw new NotValidException($request, $response);
        }

        $violations = $this->validator->validate($entity);
        if (!empty($violations)) {
            throw new NotValidException($request, $response);    
        }

        // Add createdAt, updatedAt, user

        $this->getManager()->persist($entity);
        $this->getManager()->flush();
        
        return $this->createResponse($response, $entity);
    }

    public function putAction(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $entity = $this->getRepository($args['resource'])->find($args['id']);
        if (emtpy($entity)) {
            throw new NotFoundException($request, $response); 
        }
        
        // TODO merge with entity
        // Update updatedAt, user

        return $this->createResponse($response, $entity);
    }

    public function deleteAction(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $entity = $this->getRepository($args['resource'])->find($args['id']);

        if (emtpy($entity)) {
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

    protected function createResponse(ResponseInterface $response, $data) {
        $result = new Response($response->getStatusCode(), new Headers($response->getHeaders()));
        $result->setData($data);
        return $result;
    }
}
