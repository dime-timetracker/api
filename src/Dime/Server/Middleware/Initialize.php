<?php
namespace Dime\Server\Middleware;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Connectors\ConnectionFactory;
use Illuminate\Database\ConnectionResolver;
use Illuminate\Database\Eloquent\Model;
use Slim\Middleware;

class Initialize extends Middleware
{
    
    public function call() {
        $app = $this->app;
        $app->log->debug('Call middleware [Initialize]');

        $capsule = new Capsule;
        $capsule->addConnection($app->config('database'));
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        $this->next->call();
    }
}
