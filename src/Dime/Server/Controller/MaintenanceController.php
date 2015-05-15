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
        $this->app->get('/migrate', [$this, 'migrateAction']);
        $this->app->get('/update', [$this, 'updateAction']);
    }

    public function migrateAction()
    {
        $repository = new \Illuminate\Database\Migrations\DatabaseMigrationRepository($this->app->database->getDatabaseManager(), 'migrations');
        if (!$repository->repositoryExists()) {
            $repository->createRepository();
        }

        $migrator = new \Illuminate\Database\Migrations\Migrator(
            $repository,
            $this->app->database->getDatabaseManager(),
            new \Illuminate\Filesystem\Filesystem()
        );

        $migrator->run($this->app->config('migration_dir'));
    }

    public function updateAction()
    {
        // TODO
    }
}
