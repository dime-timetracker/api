<?php

namespace Dime\Server\Controller;

use Dime\Server\Controller\SlimController;
use Dime\Server\Middleware\Authorization;
use Dime\Server\Middleware\Route;
use Dime\Server\Middleware\ContentType;
use Dime\Server\Model\Factory as ModelFactory;
use Slim\Slim;

/**
 * Resource controller defined the rest api based on a resource name.
 *
 * @todo save relations
 */
class ResourceController implements SlimController
{

    use \Dime\Server\Traits\Renderer;

    /**
     * @var Slim
     */
    protected $app;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var ModelFactory
     */
    protected $modelFactory;

    /**
     * Enables controller and set routes
     * @param Slim $app
     */
    public function enable(Slim $app)
    {
        $this->app = $app;
        $this->config = $this->app->config('api');
        $this->modelFactory = new ModelFactory($this->config['resources']);

        // Middleware
        $this->app->add(new Route($this->config['prefix'], new Authorization($this->app->config('auth'))));
        $this->app->add(new Route($this->config['prefix'], new ContentType($this->config['headers'])));

        // Routes
        $this->app
                ->get($this->config['prefix'] . '/:resource/:id', [$this, 'beforeAction'], [$this, 'getAction'])
                ->name('resource_get')
                ->conditions(['id' => '\d+']);

        $this->app
                ->get($this->config['prefix'] . '/:resource', [$this, 'beforeAction'], [$this, 'listAction'])
                ->name('resource_list');

        $this->app
                ->put($this->config['prefix'] . '/:resource/:id', [$this, 'beforeAction'], [$this, 'putAction'])
                ->name('resource_put')
                ->conditions(['id' => '\d+']);

        $this->app
                ->post($this->config['prefix'] . '/:resource', [$this, 'beforeAction'], [$this, 'postAction'])
                ->name('resource_post');

        $this->app
                ->delete($this->config['prefix'] . '/:resource/:id', [$this, 'beforeAction'], [$this, 'deleteAction'])
                ->name('resource_delete')
                ->conditions(['id' => '\d+']);
    }

    public function beforeAction(\Slim\Route $route)
    {
        $resource = $route->getParam('resource');
        if (!array_key_exists($resource, $this->config['resources'])) {
            $this->app->halt(404, json_encode(['error' => 'Resource [' . $resource . '] not found.']));
        }
    }

    /**
     * [GET] /$resource
     * @param string $resource
     */
    public function listAction($resource)
    {
        $modelClass = $this->modelFactory->with($resource);
        $collection = $modelClass
            ->where('user_id', $this->app->user->id)
            ->ordered();

        // Request parameter
        $filter = $this->app->request()->get('filter');
        if (!empty($filter)) {
            $collection = $collection->filtered($filter);
        }

        $page = $this->app->request()->get('page', 1);
        $with = $this->app->request()->get('with', -1);
        $collection = $this->paged($resource, $collection, $page, $with, $filter);
        $this->render($collection->get()->toArray());
    }

    /**
     * [GET] /$resource/$id
     * @param string $resource
     * @param int $id
     */
    public function getAction($resource, $id)
    {
        $modelClass = $this->modelFactory->with($resource);
        $model = $modelClass
                ->where('user_id', $this->app->user->id)
                ->find($id);
        if (is_null($model)) {
            $this->render(['error' => 'Not found'], 404);
        } else {
            $this->render($model->toArray());
        }
    }

    /**
     * [POST] /$resource/
     * @param type $resource
     * @return type
     */
    public function postAction($resource)
    {
        $data = $this->app->request->getBody();
        if (empty($data)) {
            $this->render(['error' => 'Data not valid'], 400);
        } else {
            $model = $this->modelFactory->createWith($resource, $data, $this->app->user->id);
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
        $modelClass = $this->modelFactory->with($resource);
        $model = $modelClass->where('user_id', $this->app->user->id)->find($id);
        if (is_null($model)) {
            $this->render(['error' => 'Not found'], 404);
        } else {
            $data = $this->app->request->getBody();
            if (empty($data)) {
                $this->render(['error' => 'Data not valid'], 400);
            } else {
                $model->fill($data);
                $this->modelFactory->updateRelations($resource, $model, $data, $this->app->user->id)
                    ->save();
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
        $modelClass = $this->modelFactory->getClass($resource);
        $model = $modelClass::where('user_id', $this->app->user->id)->find($id);
        if (is_null($model)) {
            $this->render(['error' => 'Not found'], 404);
        } else {
            $model->delete();
            $this->render($model->toArray());
        }
    }

    /**
     * Page collection
     *
     * @param type $resource
     * @param type $collection
     * @param int $page
     * @param int $with
     * @param array $filter
     * @return type
     */
    protected function paged($resource, $collection, $page, $with, $filter)
    {
        if (isset($this->modelFactory[$resource]['pageSize']) && $with < 1) {
            $with = $this->modelFactory[$resource]['pageSize'];
        }

        $total    = $collection->count();
        $this->app->response()->headers()->set('X-Dime-Total', $total);

        if ($with > 0) {
            $lastPage = ceil($total / $with);
            $this->app->response()->headers()->set('Link', implode(', ', [
                $this->pageUrl($resource, $filter, ($page + 1), $with, 'next'),
                $this->pageUrl($resource, $filter, ($page + 1), $with, 'prev'),
                $this->pageUrl($resource, $filter, 1, $with, 'first'),
                $this->pageUrl($resource, $filter, $lastPage, $with, 'last'),
            ]));

            $collection = $collection->take($with)->skip($with * ($page - 1));
        }

        return $collection;
    }

    /**
     * Generate page urls.
     *
     * @param type $resource
     * @param type $filter
     * @param type $page
     * @param type $with
     * @param type $rel
     * @return type
     */
    protected function pageUrl($resource, $filter, $page = 1, $with = 30, $rel = null)
    {
        $param = [];
        if (!empty($filter)) {
            $param[] = 'filter='. urlencode($filter);
        }
        if (!empty($page)) {
            $param[] = 'page='. urlencode($page);
        }
        if (!empty($with)) {
            $param[] = 'with='. urlencode($with);
        }

        $uri = $this->app->urlFor('resource_list', array('resource' => $resource));
        if (!empty($param)) {
            $uri .= '?' . join('&', $param);
        }

        return '<' . $uri . '>' . ( (!empty($rel)) ? '; rel="' . $rel . '"' : '' );
    }

}
