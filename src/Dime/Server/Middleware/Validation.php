<?php

namespace Dime\Server\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Validation implements Middleware
{
    use \Dime\Server\Traits\ResponseTrait;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }


    public function run(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $entity = $request->getParsedBody();

        if (empty($entity)) {
            return $this->createResponse($response, ['message' => 'Bad request'], 400);
        }

        $errors = $this->checkErrors($this->validator->validate($entity));
        if (!empty($errors)) {
            return $this->createResponse($response, $errors, 400);
        }

        return $next($request, $response);
    }

    protected function checkErrors($errors)
    {
        $result = [];
        foreach($errors as $error) {
            $result[$error->getPropertyPath()] = $error->getMessage();
        }
        return $result;
    }
}
