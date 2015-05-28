<?php

namespace Dime\Server\Middleware;

use Exception;
use Dime\Server\View\Json;
use Slim\Middleware;

/**
 * ContentType
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
        $this->mediaType = $this->extractMediaType();
        if ($this->mediaType) {
            $env = $this->app->environment();
            $this->install($this->mediaType);
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
     * Extract request type from accept header if content-type is not available.
     *
     * @return type content-type or accept header
     */
    public function extractMediaType() {
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

    /**
     * Install view and error handler
     * 
     * @param type $mediaType
     */
    public function install($mediaType)
    {
        $app = $this->app;
        switch ($mediaType) {
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
