<?php

namespace Dime\Server\Middleware;

use Exception;
use Dime\Server\View\Json;
use Slim\Middleware;

/**
 * ContentType Middleware read the accept header of the request,
 * install view and decode content on its content-type.
 * At the moment only "application/json" is supported.
 *
 * @author Danilo Kuehn <dk@nogo-software.de>
 */
class ContentType extends Middleware
{

    protected $headers = [];

    public function __construct(array $headers = [])
    {
        $this->headers = $headers;
    }

    public function call()
    {
        $acceptHeader = $this->extractAcceptHeader();
        if (!empty($acceptHeader)) {
            $this->install($acceptHeader);
        }

        $contentType = $this->app->request()->getMediaType();
        if (!empty($contentType)) {
            $env = $this->app->environment();
            $env['slim.input_original'] = $env['slim.input'];
            $env['slim.input'] = $this->decode($contentType, $env['slim.input']);
        }

        $this->next->call();

        if (isset($this->headers)) {
            foreach ($this->headers as $key => $value) {
                $this->app->response()->headers()->set($key, $value);
            }
            $this->app->contentType($acceptHeader);
        } else {
            $this->app->contentType('text/html');
        }
    }

    /**
     * Extract request type from accept header if content-type is not available.
     *
     * @return type content-type or accept header
     */
    public function extractAcceptHeader() {
        $mediaType = $this->app->request()->getMediaType();
        if (empty($mediaType)) {
            $acceptParts = preg_split('/\s*[;,]\s*/', $this->app->request()->headers('accept'));
            $mediaType = strtolower($acceptParts[0]);
        }
        return $mediaType;
    }

    /**
     * Decode data by given media type
     * 
     * @param type $acceptHeader
     * @param type $data
     * @return type
     */
    public function decode($acceptHeader, $data)
    {
        $result = $data;
        switch ($acceptHeader) {
            case 'application/json':
                $decoded = json_decode($data, true);
                if (JSON_ERROR_NONE === json_last_error()) {
                    $result = $decoded;
                }
                break;
        }

        return $result;
    }

    /**
     * Install view and error handler
     *
     * @param type $acceptHeader
     */
    public function install($acceptHeader)
    {
        $app = $this->app;
        switch ($acceptHeader) {
            case 'application/json':
                // Error handler
                $this->app->error(function (Exception $e) use ($app) {
                    $app->render(500, array(
                        'error' => true,
                        'msg' => ContentType::_errorType($e->getCode()) . ": " . $e->getMessage(),
                        'line' => $e->getLine(),
                        'file' => $e->getFile()
                    ));
                });

                $this->app->notFound(function() use ($app) {
                    $app->render(404, array(
                        'error' => true,
                        'msg' => 'Invalid route',
                    ));
                });

                // View
                $this->app->view(new Json());
                break;
        }
    }

    /**
     * Translate error number into string
     * @param type $type
     * @return string
     */
    static function _errorType($type = 1)
    {
        switch ($type) {
            default:
            case E_ERROR: // 1 //
                return 'ERROR';
            case E_WARNING: // 2 //
                return 'WARNING';
            case E_PARSE: // 4 //
                return 'PARSE';
            case E_NOTICE: // 8 //
                return 'NOTICE';
            case E_CORE_ERROR: // 16 //
                return 'CORE_ERROR';
            case E_CORE_WARNING: // 32 //
                return 'CORE_WARNING';
            case E_CORE_ERROR: // 64 //
                return 'COMPILE_ERROR';
            case E_CORE_WARNING: // 128 //
                return 'COMPILE_WARNING';
            case E_USER_ERROR: // 256 //
                return 'USER_ERROR';
            case E_USER_WARNING: // 512 //
                return 'USER_WARNING';
            case E_USER_NOTICE: // 1024 //
                return 'USER_NOTICE';
            case E_STRICT: // 2048 //
                return 'STRICT';
            case E_RECOVERABLE_ERROR: // 4096 //
                return 'RECOVERABLE_ERROR';
            case E_DEPRECATED: // 8192 //
                return 'DEPRECATED';
            case E_USER_DEPRECATED: // 16384 //
                return 'USER_DEPRECATED';
        }
    }

}
