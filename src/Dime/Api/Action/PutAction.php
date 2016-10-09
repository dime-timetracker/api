<?php

namespace Dime\Api\Action;

use League\Container\ContainerInterface;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\NotFoundException;
use Slim\Exception\SlimException;

class PutAction implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $repository = $this->get($args['resource'] . '_repository');
        $identifier = [
            'id' => $args['id'],
            'user_id' => $this->get('session')->getUserId()
        ];

        $result = $repository->find($identifier);
        if ($result === FALSE) {
            throw new NotFoundException($request, $response);
        }

        $parsedData = $request->getParsedBody();
        if (empty($parsedData)) {
            throw new \Exception("No data");
        }

        // Tranform and behave
        $behavedData = \Dime\Server\Stream::of($parsedData)
                ->append(new \Dime\Server\Behavior\Timestampable(null))
                ->append($this->get('assignable'))
                ->collect();

        // Validate
        if ($this->has($args['resource'] . '_validator')) {
            $errors = $this->get($args['resource'] . '_validator')->validate($behavedData);
            if (!empty($errors)) {
                return $this->get('responder')->respond($response, $errors, 400);
            }
        }

        try {
            $repository->update($behavedData, $identifier);
        } catch (\Exception $e) {
            var_dump($e);
        }

        $result = $repository->find($identifier);

        return $this->get('responder')->respond($response, $result);
    }
}
