<?php

namespace Dime\Server\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use JMS\Serializer\SerializerInterface;

/**
 * ContentNegotiation is a middleware to check to requirements.
 * 
 * Tasks:
 * - MUST extract Accept header
 * - MUST check Accept header
 * - MUST deserialize on content-type
 * - MUST serialize on content-type
 * 
 * @author Danilo Kuehn <dk@nogo-software.de>
 */
class ContentNegotiation implements Middleware
{

    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var string
     */
    protected $type = 'array';
    protected $accept = 'application/json';
    protected $allowedAcceptHeaders = [
        'application/json',
        'application/xml',
        'text/xml',
        'text/yml',
        'text/html'
    ];

    public function __construct(array $config, SerializerInterface $serializer)
    {
        $this->config = $config;
        $this->serializer = $serializer;
    }

    public function run(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $resourceType = $request->getAttribute('resourceType');
        if (!empty($resourceType)) {
            $this->type = $resourceType['entity'];
        }
        $this->matchAcceptHeader($request);
        $request = $this->parseBody($request);
        $request = $this->installSerializer($request);

        $response = $next($request, $response);

        $response = $this->installResponseHeader($response);

        return $response;
    }

    protected function parseBody(ServerRequestInterface $request)
    {
        if ($request->getBody()->getSize() == null) {
            return $request;
        }
        try {
            $parsedBody = $this->serializer->deserialize($request->getBody(), $this->type, $this->serialierType());
        } catch (Exception $ex) {
            $parsedBody = $request->getBody();
        }
        
        return $request->withParsedBody($parsedBody);
    }

    protected function matchAcceptHeader(ServerRequestInterface $request)
    {
        $acceptHeader = $request->getHeader('accept');

        if (!empty($acceptHeader)) {
            $found = false;
            $acceptHeader = preg_split('/\s*[;,]\s*/', $acceptHeader[0]);
            foreach ($acceptHeader as $accept) {
                foreach ($this->allowedAcceptHeaders as $name) {
                    if (trim($accept) === $name) {
                        $this->accept = $name;
                        $found = true;
                        break;
                    }
                }
                if ($found) {
                    break;
                }
            }
        }
    }

    protected function installSerializer(ServerRequestInterface $request)
    {
        $serializer = $this->serializer;
        $type = $this->serialierType();        

        return $request->withAttribute('serializer', function ($content) use ($serializer, $type) {
                    return $serializer->serialize($content, $type);
                });
    }

    protected function installResponseHeader(ResponseInterface $response)
    {
        if (isset($this->config['headers']) && !empty($this->config['headers'])) {
            foreach ($this->config['headers'] as $name => $value) {
                $response = $response->withHeader($name, $value);
            }
            $response = $response->withHeader('Content-Type', $this->accept);
        } else {
            $response = $response->withHeader('Content-Type', 'text/html');
        }
        return $response;
    }
    
    protected function serialierType() {
        $type = 'json';
        switch ($this->accept) {
            case 'application/xml':
                $type = 'xml';
                break;
            case 'text/xml':
                $type = 'xml';
                break;
            case 'text/yml':
                $type = 'yml';
                break;
        }
        return $type;
    }

}
