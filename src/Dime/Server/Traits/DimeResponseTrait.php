<?php

namespace Dime\Server\Traits;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Headers;
use Dime\Server\Http\Response;

trait DimeResponseTrait
{
    protected function createResponse(ResponseInterface $response, $data, $status = 200) {
        $result = new Response($status, new Headers($response->getHeaders()));
        $result->setData($data);
        return $result;
    }
}
