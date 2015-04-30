<?php

namespace Dime\Server\Controller;

use Hampel\Json\Json;
use Hampel\Json\JsonException;
use Dime\Server\Controller\SlimController;
use Dime\Server\Middleware\Initialize;
use Dime\Server\Middleware\AuthBasic;
use Dime\Server\Middleware\ResourceIdentifier;
use Slim\Slim;

/**
 * Resource controller defined the rest api based on a resource name.
 *
 * @todo save relations
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
        $this->app->add(new AuthBasic($this->app->config('auth')));
        $this->app->add(new Initialize());
        $this->app->add(new ResourceIdentifier($this->config));

        $this->app
                ->get($this->config['prefix'] . '/:resource/:id', [$this, 'getAction'])
                ->name('resource_get')
                ->conditions(['id' => '\d+']);

        $this->app
                ->get($this->config['prefix'] . '/:resource/page/:page', [$this, 'listAction'])
                ->name('resource_list_page')
                ->conditions(['page' => '\d+']);

        $this->app
                ->get($this->config['prefix'] . '/:resource/page/:page/with/:with', [$this, 'listAction'])
                ->name('resource_list_page_with')
                ->conditions(['page' => '\d+', 'with' => '\d+']);

        $this->app
                ->get($this->config['prefix'] . '/:resource', [$this, 'listAction'])
                ->name('resource_list');

        $this->app
                ->put($this->config['prefix'] . '/:resource/:id', [$this, 'putAction'])
                ->name('resource_put')
                ->conditions(['id' => '\d+']);

        $this->app
                ->post($this->config['prefix'] . '/:resource', [$this, 'postAction'])
                ->name('resource_post');

        $this->app
                ->delete($this->config['prefix'] . '/:resource/:id', [$this, 'deleteAction'])
                ->name('resource_delete')
                ->conditions(['id' => '\d+']);
    }

    /**
     * [GET] /$resource
     * @param string $resource
     * @param int $page
     * @param int $with
     */
    public function listAction($resource, $page = 1, $with = 30)
    {
        $modelClass = $this->modelWithRelations($resource);
        $collection = $modelClass
                ->where('user_id', $this->app->user->id)
                ->latest('updated_at')
                ->take($with)
                ->skip($with * ($page - 1))
                ->get();

        $total = $collection->count();
        $lastPage = ceil($total / $with);
        $this->app->response()->headers()->set('X-Dime-Total', $total);
        $this->app->response()->headers()->set('X-Dime-Link', implode(', ', [
            $this->pageUrl($resource, 1, $with, 'first'),
            $this->pageUrl($resource, $lastPage, $with, 'last'),
            $this->pageUrl($resource, ($page + 1), $with, 'next'),
            $this->pageUrl($resource, ($page + 1), $with, 'previous')
        ]));

        $this->render($collection->toJson());
    }

    /**
     * [GET] /$resource/$id
     * @param string $resource
     * @param int $id
     */
    public function getAction($resource, $id)
    {
        $modelClass = $this->modelWithRelations($resource);
        $model = $modelClass
                ->where('user_id', $this->app->user->id)
                ->find($id);
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
            $this->render(['msg' => 'Data not valid'], 400);
            return;
        }

        $modelClass = $this->modelClass($resource);

        $model = new $modelClass();
        $model->fill($request_data);
        $model->user_id = $this->app->user->id;

        $model->save();

        $this->render($model->toJson());
    }

    /**
     * [PUT] /$resource/$id
     * @param string $resource
     * @param int $id
     * @return type
     */
    public function putAction($resource, $id)
    {
        $modelClass = $this->modelClass($resource);
        $model = $modelClass::where('user_id', $this->app->user->id)
                ->find($id);

        if ($model === false) {
            $this->render(['msg' => 'Not found'], 404);
            return;
        }

        $request_data = $this->json();
        if (empty($request_data)) {
            $this->render(['msg' => 'Data not valid'], 400);
            return;
        }

        $model->fill($request_data);
        $model->save();

        $this->render($model->toJson());
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
        $model = $modelClass::where('user_id', $this->app->user->id)
                ->find($id);
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
        // Status
        $this->app->response()->setStatus($status);

        // Header
        $header = $this->config['headers'];
        foreach ($header as $key => $value) {
            $this->app->response()->headers()->set($key, $value);
        }

        if (is_array($data)) {
            $this->app->response()->body(json_encode($data));
        } else {
            $this->app->response()->body($data);
        }
    }

    protected function pageUrl($resource, $page = 1, $with = 30, $rel = null)
    {
        $url = $this->app->urlFor('resource_list_page_with', array('resource' => $resource, 'page' => $page, 'with' => $with));

        if (!empty($rel)) {
            $url .= '; rel=' . $rel;
        }

        return $url;
    }

    protected function modelClass($name)
    {
        $result = NULL;
        if (isset($this->config['resources'][$name])) {
            $result = $this->config['resources'][$name]['model'];
        }
        return $result;
    }

    protected function modelWithRelations($name)
    {
        $result = NULL;
        if (isset($this->config['resources'][$name])) {
            $result = $this->config['resources'][$name]['model'];
        }
        if ($result !== NULL) {
            $with = array();
            if (isset($this->config['resources'][$name]) && isset($this->config['resources'][$name]['with'])) {
                $with = $this->config['resources'][$name]['with'];
            }
            $result = $result::with($with);
        }
        return $result;
    }

}
