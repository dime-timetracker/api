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
        $this->installContentConverter($request);
        $request = $this->installSerializer($request);

        $response = $next($request, $response);
        $response = $this->installResponseHeader($response);

        return $response;
    }

    protected function installContentConverter(ServerRequestInterface $request)
    {
        $request->registerMediaTypeParser('application/json', function ($input) {
          return $this->serializer->deserialize($input, $this->type, 'json');
        });
        $request->registerMediaTypeParser('application/xml', function ($input) {
            return $this->serializer->deserialize($input, $this->type, 'xml');
        });
        $request->registerMediaTypeParser('text/xml', function ($input) {
            return $this->serializer->deserialize($input, $this->type, 'xml');
        });
        $request->registerMediaTypeParser('text/yml', function ($input) {
            return $this->serializer->deserialize($input, $this->type, 'yml');
        });
        $request->registerMediaTypeParser('text/html', function ($input) {
            return $this->serializer->deserialize($input, $this->type, 'json');
        });
        
        return $request;
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
        
        return $request->withAttribute('serializer', function ($content) use ($type) {
            return $this->serializer->serialize($content, $type);
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

}
