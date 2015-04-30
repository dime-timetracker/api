<?php

namespace Dime\Server\Controller;

use Dime\Server\Controller\SlimController;
use Dime\Server\Middleware\Database;
use Dime\Server\Middleware\AuthBasic;
use Dime\Server\Middleware\Json as JsonMiddleware;
use Dime\Server\Middleware\ResourceIdentifier;
use Dime\Server\Resource\Factory;
use Dime\Server\View\Json as JsonView;
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
     * @var Factory
     */
    protected $factory;

    /**
     * Enables controller and set routes
     * @param Slim $app
     */
    public function enable(Slim $app)
    {
        $this->app = $app;
        $this->config = $this->app->config('api');
        $this->factory = new Factory($this->config['resources']);

        // Middleware
        $this->app->add(new AuthBasic($this->app->config('auth')));
        $this->app->add(new JsonMiddleware($this->config));
        $this->app->add(new Database());
        $this->app->add(new ResourceIdentifier($this->config));

        // Routes
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
        $modelClass = $this->factory->with($resource);
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

        $this->render($collection->toArray());
    }

    /**
     * [GET] /$resource/$id
     * @param string $resource
     * @param int $id
     */
    public function getAction($resource, $id)
    {
        $modelClass = $this->factory->with($resource);
        $model = $modelClass
                ->where('user_id', $this->app->user->id)
                ->find($id);
        $this->render($model->toArray());
    }

    /**
     * [POST] /$resource/
     * @param type $resource
     * @return type
     */
    public function postAction($resource)
    {
        $env = $this->app->environment();
        if (empty($env['slim.input'])) {
            $this->render(['error' => 'Data not valid'], 400);
        } else {
            $modelClass = $this->factory->getClass($resource);

            $model = new $modelClass();
            $model->fill($env['slim.input']);
            $model->user_id = $this->app->user->id;

            $model->save();

            $this->render($model->toArray());
        }
    }

    /**
     * [PUT] /$resource/$id
     * @param string $resource
     * @param int $id
     * @return type
     */
    public function putAction($resource, $id)
    {
        $modelClass = $this->factory->with($resource);
        $model = $modelClass->where('user_id', $this->app->user->id)->find($id);
        if (is_null($model)) {
            $this->render(['error' => 'Not found'], 404);
        } else {
            $env = $this->app->environment();
            if (empty($env['slim.input'])) {
                $this->render(['error' => 'Data not valid'], 400);
            } else {
                $model->fill($env['slim.input']);
                $model->save();
                $this->render($model->toArray());
            }
        }
    }

    /**
     * [DELETE] /$resource/$id
     * @param type $resource
     * @param type $id
     * @return type
     */
    public function deleteAction($resource, $id)
    {
        $modelClass = $this->factory->getClass($resource);
        $model = $modelClass::where('user_id', $this->app->user->id)->find($id);
        if (is_null($model)) {
            $this->render(['error' => 'Not found'], 404);
        } else {
            $model->delete();
            $this->render($model->toArray());
        }
    }

    /**
     * Render content and status
     *
     * @param mixed $data
     * @param int $status
     */
    protected function render($data, $status = 200)
    {
        $this->app->view(new JsonView());

        $this->app->response()->setStatus($status);

        $this->app->render('', $data);
    }

    protected function pageUrl($resource, $page = 1, $with = 30, $rel = null)
    {
        $url = $this->app->urlFor('resource_list_page_with', array('resource' => $resource, 'page' => $page, 'with' => $with));

        if (!empty($rel)) {
            $url .= '; rel=' . $rel;
        }

        return $url;
    }

}
