<?php

namespace Dime\Server\Controller;

use Dime\Server\Controller\SlimController;
use Slim\Slim;

class ParseController implements SlimController
{

    /**
     * @var Slim
     */
    protected $app;

    public function enable(Slim $app)
    {
        $this->app = $app;
        $this->app->post('/parse', [$this, 'executeAction']);
    }

    public function executeAction()
    {
        // TODO 
    }

}
