<?php

namespace Dime\Server\Http;

use Slim\Http\Request as SlimRequest;

class Request extends SlimRequest
{
    public function getParsedBody()
    {
        if (!$this->body) {
            return null;
        }

        $mediaType = $this->getMediaType();
        $body = (string)$this->getBody();

        if (isset($this->bodyParsers[$mediaType]) === true) {
            $parsed = $this->bodyParsers[$mediaType]($body);

            if (!is_null($parsed) && !is_object($parsed) && !is_array($parsed)) {
                throw new RuntimeException(
                    'Request body media type parser return value must be an array, an object, or null'
                );
            }
            $this->bodyParsed = $parsed;
        }

        return $this->bodyParsed;
    }
}
