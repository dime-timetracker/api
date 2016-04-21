<?php

namespace Dime\Server\Responder;

use Psr\Http\Message\ResponseInterface;

class JsonResponder implements ResponderInterface
{
    public function respond(ResponseInterface $response, array $data, $status = 200) {
        if ($status !== 200) {
            $response = $response->withStatus($status);
        }

        $response->getBody()->write(
            json_encode(
                $data,
                JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE
            )
        );
        return $response;
    }
}
