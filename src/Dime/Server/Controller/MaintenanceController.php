<?php

namespace Dime\Server\Controller;

use Dime\Server\Controller\SlimController;
use Slim\Slim;

class MaintenanceController implements SlimController
{
    /**
     * @var Slim
     */
    protected $app;

    public function enable(Slim $app)
    {
        $this->app = $app;
        $this->app->post('/install', [$this, 'installAction']);
        $this->app->post('/update', [$this, 'updateAction']);
    }

    public function installAction()
    {
        // TODO
    }

    public function updateAction()
    {
        // TODO
    }
}
