<?php

namespace Dime\Server\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * ContentNegotiation is a middleware to check to requirements.
 * 
 * Tasks:
 * - MUST extract Accept header
 * - MUST check Accept header
 * 
 * @author Danilo Kuehn <dk@nogo-software.de>
 */
class ContentNegotiation implements MiddlewareInterface
{
    use \Dime\Server\Traits\ConfigurationTrait;

    /**
     * @var string
     */
    protected $accept = 'application/json';
    protected $allowedAcceptHeaders = [
        'application/json',
        'application/xml',
        'text/xml',
        'text/yml',
        'text/html'
    ];

    public function __construct(array $config)
    {
        $this->setConfig($config);
    }

    public function run(ServerRequestInterface $request, ResponseInterface $response, callable $next)
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

        return $this->installResponseHeader(
            $next($request->withAttribute('acceptType', $this->accept), $response)
        );
    }

    protected function installResponseHeader(ResponseInterface $response)
    {
        $headers = $this->getConfigValue(['headers']);
        if (!empty($headers)) {
            foreach ($headers as $name => $value) {
                $response = $response->withHeader($name, $value);
            }
            $response = $response->withHeader('Content-Type', $this->accept);
        } else {
            $response = $response->withHeader('Content-Type', 'text/html');
        }
        return $response;
    }
}
