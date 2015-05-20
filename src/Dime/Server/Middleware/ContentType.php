<?php

namespace Dime\Server\Middleware;

use Slim\Middleware;

/**
 * ApiMiddleware
 *
 * @author Danilo Kuehn <dk@nogo-software.de>
 */
class ContentType extends Middleware
{

    protected $headers = [];
    protected $mediaType = 'text/html';

    public function __construct(array $headers = [])
    {
        $this->headers = $headers;
    }

    public function call()
    {
        $this->mediaType = $this->app->request()->getMediaType();

        if ($this->mediaType) {
            $env = $this->app->environment();
            $env['slim.input_original'] = $env['slim.input'];
            $env['slim.input'] = $this->decode($this->mediaType, $env['slim.input']);
        }

        $this->next->call();

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
