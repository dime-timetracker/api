<?php

namespace Dime\Server\Controller;

use Hampel\Json\Json;
use Hampel\Json\JsonException;
use Dime\Server\Database\Repository;
use Dime\Server\Middleware\Initialize;
use Dime\Server\Middleware\ResourceIdentifier;
use Dime\Server\Controller\SlimController;
use Slim\Slim;

class Resource implements SlimController
{

    /**
     * @var Slim
     */
    protected $app;

    public function enable(Slim $app)
    {
        $this->app = $app;
        $api_config = $this->app->config('api');

        // Routes
        $this->app->add(new Initialize());
        $this->app->add(new ResourceIdentifier($api_config['prefix']));
        $this->app->get($api_config['prefix'] . '/:resource/:id', [$this, 'getAction'])->conditions(['id' => '\d+']);
        $this->app->get($api_config['prefix'] . '/:resource', [$this, 'listAction']);
        $this->app->put($api_config['prefix'] . '/:resource/:id', [$this, 'putAction'])->conditions(['id' => '\d+']);
        $this->app->post($api_config['prefix'] . '/:resource', [$this, 'postAction']);
        $this->app->delete($api_config['prefix'] . '/:resource/:id', [$this, 'deleteAction'])->conditions(['id' => '\d+']);
    }

    public function listAction($resource)
    {
        $c = \Dime\Server\Model\Activity::with('customer', 'project', 'service', 'tags', 'timeslices')->get();
        $this->render($c->toJson());
    }

    public function getAction($resource, $id)
    {
        $this->render($this->repository($resource)->find($id));
    }

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

    public function deleteAction($resource, $id)
    {
        $repository = $this->repository($resource);

        $result = $repository->find($id);
        if ($result === false) {
            $this->render([ 'msg' => 'Not found'], 404);
            return;
        } else {
            $repository->remove($id);
            $this->render($result);
        }
    }

    protected function repository($name)
    {
        return new Repository(
                $name, $this->app->schemas->fetchTableCols($name), $this->app->connection, $this->app->queries
        );
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

}
