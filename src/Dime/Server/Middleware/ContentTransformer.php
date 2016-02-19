<?php

namespace Dime\Server\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use JMS\Serializer\SerializerInterface;

use Dime\Server\Http\Response;

class ContentTransformer implements Middleware
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    protected $accept = 'application/json';

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function run(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $this->accept = $request->getAttribute('acceptType', $this->accept);
        return $this->serialize($next($this->deserialize($request), $response));
    }

    protected function deserialize(ServerRequestInterface $request)
    {
        if ($request->getBody()->getSize() == null) {
            return $request;
        }

        try {
            $parsedBody = $this->serializer->deserialize(
                $request->getBody(),
                $request->getAttribute('resourceType', 'array'),
                $this->getFormat($request->getHeader('content-type'))
            );
        } catch (Exception $ex) {
            $parsedBody = $request->getBody();
        }

        return $request->withParsedBody($parsedBody);
    }

    protected function serialize(ResponseInterface $response)
    {
        if ($response instanceof Response) {
            try {
                $response->write(
                    $this->serializer->serialize(
                        $response->getData(),
                        $this->getFormat($this->accept)
                    ),
                    true
                );
            } catch (Exception $ex) {
                
            }
        }

        return $response;
    }

    protected function getFormat($type)
    {
        switch ($type) {
            case 'application/xml':
                $format = 'xml';
                break;
            case 'text/xml':
                $format = 'xml';
                break;
            case 'text/yml':
                $format = 'yml';
                break;
            default:
                $format = 'json';
        }
        return $format;
    }
}
