<?php

namespace Dime\Server\Controller;

use Hampel\Json\Json;
use Hampel\Json\JsonException;
use Dime\Server\Controller\SlimController;
use Dime\Server\Middleware\Initialize;
use Dime\Server\Middleware\ResourceIdentifier;
use Slim\Slim;

/**
 * Resource controller defined the rest api based on a resource name.
 *
 * @todo POST implementation
 * @todo PUT implementation
 * @todo abstract resource/repository
 */
class ResourceController implements SlimController
{

    /**
     * @var Slim
     */
    protected $app;

    /**
     * @var array
     */
    protected $config;

    /**
     * Enables controller and set routes
     * @param Slim $app
     */
    public function enable(Slim $app)
    {
        $this->app = $app;
        $this->config = $this->app->config('api');

        // Routes
        $this->app->add(new Initialize());
        $this->app->add(new ResourceIdentifier($this->config));
        $this->app->get($this->config['prefix'] . '/:resource/:id', [$this, 'getAction'])->conditions(['id' => '\d+']);
        $this->app->get($this->config['prefix'] . '/:resource', [$this, 'listAction']);
        $this->app->put($this->config['prefix'] . '/:resource/:id', [$this, 'putAction'])->conditions(['id' => '\d+']);
        $this->app->post($this->config['prefix'] . '/:resource', [$this, 'postAction']);
        $this->app->delete($this->config['prefix'] . '/:resource/:id', [$this, 'deleteAction'])->conditions(['id' => '\d+']);
    }

    /**
     * [GET] /$resource/
     * @param string $resource
     */
    public function listAction($resource)
    {
        $modelClass = $this->modelClass($resource);
        $collection = $modelClass::with($this->modelRelations($resource))->get();
        $this->render($collection->toJson());
    }

    /**
     * [GET] /$resource/$id
     * @param string $resource
     * @param int $id
     */
    public function getAction($resource, $id)
    {
        $modelClass = $this->modelClass($resource);
        $model = $modelClass::with($this->modelRelations($resource))->find($id);
        $this->render($model->toJson());
    }

    /**
     * [POST] /$resource/
     * @param type $resource
     * @return type
     */
    public function postAction($resource)
    {
        $request_data = $this->json();
        if (empty($request_data)) {
            $this->render([ 'msg' => 'Data not valid'], 400);
            return;
        }

        $repository = $this->repository($resource);
        $identifier = $repository->persist($request_data);
        $this->render($repository->find($identifier));
    }

    /**
     * [PUT] /$resource/$id
     * @param string $resource
     * @param int $id
     * @return type
     */
    public function putAction($resource, $id)
    {
        $repository = $this->repository($resource);

        $result = $repository->find($id);
        if ($result === false) {
            $this->render([ 'msg' => 'Not found'], 404);
            return;
        }

        $request_data = $this->json();
        if (empty($request_data)) {
            $this->render([ 'msg' => 'Data not valid'], 400);
            return;
        }

        $identifier = $repository->persist($request_data);
        $this->render($repository->find($identifier));
    }

    /**
     * [DELETE] /$resource/$id
     * @param type $resource
     * @param type $id
     * @return type
     */
    public function deleteAction($resource, $id)
    {
        $modelClass = $this->modelClass($resource);
        $model = $modelClass::with($this->modelRelations($resource))->find($id);
        if ($model === false) {
            $this->render(['msg' => 'Not found'], 404);
            return;
        } else {
            $model->delete();
            $this->render($model->toJson());
        }
    }

    /**
     * Decode json data from request
     * @return mixed|null
     */
    protected function json()
    {
        try {
            $input = Json::decode(trim($this->app->request()->getBody()), true);
        } catch (JsonException $ex) {
            $this->app->log->error($ex->getMessage());
            $input = null;
        }

        return $input;
    }

    /**
     * Render content and status
     *
     * @param mixed $data
     * @param int $status
     */
    protected function render($data, $status = 200)
    {
        $this->app->contentType("application/json");
        $this->app->response()->status($status);
        $this->app->response()->body($data);
    }

    protected function modelClass($name)
    {
        $result = NULL;
        if (isset($this->config['resources'][$name])) {
            $result = $this->config['resources'][$name]['model'];
        }
        return $result;
    }

    protected function modelRelations($name)
    {
        $result = array();
        if (isset($this->config['resources'][$name])
                && isset($this->config['resources'][$name]['with'])) {
            $result = $this->config['resources'][$name]['with'];
        }
        return $result;
    }
}
