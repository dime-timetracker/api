<?php

namespace Dime\Server\Middleware;

use Slim\Middleware;

/**
 * ApiMiddleware
 *
 * @author Danilo Kuehn <dk@nogo-software.de>
 */
class ContentMiddleware extends Middleware
{

    protected $route = '/';
    protected $headers = [];
    protected $mediaType = 'text/html';

    public function __construct($route, array $headers = [])
    {
        $this->route = $route;
        $this->headers = $headers;
    }

    public function call()
    {
        if (strpos($this->app->request()->getPathInfo(), $this->route) !== false) {
            $this->app->hook('slim.before.dispatch', array($this, 'onBeforeDispatch'));
            $this->app->hook('slim.after.router', array($this, 'onAfterRouter'));
        }
        
        $this->next->call();
    }

    /**
     * Check if resource is allowed and decode data
     */
    public function onBeforeDispatch()
    {
        $this->mediaType = $this->app->request()->getMediaType();
        
        $env = $this->app->environment();
        $env['slim.input_original'] = $env['slim.input'];
        $env['slim.input'] = $this->decode($this->mediaType, $env['slim.input']);
    }

    /**
     * Add api header to response
     */
    public function onAfterRouter()
    {
        if (isset($this->headers)) {
            foreach ($this->headers as $key => $value) {
                $this->app->response()->headers()->set($key, $value);
            }
        } else {
            $this->app->contentType($this->mediaType);
        }
    }

    /**
     * Decode data by given media type
     * 
     * @param type $mediaType
     * @param type $data
     * @return type
     */
    public function decode($mediaType, $data)
    {
        $result = $data;
        switch ($mediaType) {
            case 'application/json':
                $decoded = json_decode($data, true);
                if (JSON_ERROR_NONE === json_last_error()) {
                    $result = $decoded;
                }
                break;
        }

        return $result;
    }

}
