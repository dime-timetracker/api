<?php

namespace Dime\Api\Action;

use League\Container\ContainerInterface;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\NotFoundException;
use Slim\Exception\SlimException;

class PostAction implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $repository = $this->getContainer()->get($args['resource'] . '_repository');

        $parsedData = $request->getParsedBody();
        if (json_last_error() != JSON_ERROR_NONE) {
            throw new \Exception("Request body could not parsed properly [" + json_last_error_msg() + "]");
        } else if (empty($parsedData)) {
            throw new \Exception("Request body was empty.");
        }

        // Tranform and behave
        $behavedData = \Dime\Server\Stream::of($parsedData)
                ->append(new \Dime\Server\Behavior\Timestampable())
                ->append($this->getContainer()->get('assignable'))
                ->collect();

        // Validate
        if ($this->getContainer()->has($args['resource'] . '_validator')) {
            $errors = $this->getContainer()->get($args['resource'] . '_validator')->validate($behavedData);
            if (!empty($errors)) {
                return $this->getContainer()->get('responder')->respond($response, $errors, 400);
            }
        }

        try {
            $id = $repository->insert($behavedData);
        } catch (\Exception $e) {
            throw new \Exception("Could not save new data.", $e->getCode(), $e);
        }

        $identity = [
            'id' => $id,
            'user_id' => $this->getContainer()->get('session')->getUserId()
        ];

        $result = $repository->find($identity);

        return $this->getContainer()->get('responder')->respond($response, $result);
    }
}
