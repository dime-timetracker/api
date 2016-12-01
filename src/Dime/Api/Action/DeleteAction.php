<?php

namespace Dime\Api\Action;

use League\Container\ContainerInterface;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\NotFoundException;
use Slim\Exception\SlimException;

class DeleteAction implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $repository = $this->getContainer()->get($args['resource'] . '_repository');
        $identifier = [
            'id' => $args['id'],
            'user_id' => $this->getContainer()->get('session')->getUserId()
        ];

        // Select
        $result = $repository->find($identifier);
        if ($result === FALSE) {
            throw new NotFoundException($request, $response);
        }

        // Delete
        $repository->delete($identifier);

        return $this->getContainer()->get('responder')->respond($response, $result);
    }
}
