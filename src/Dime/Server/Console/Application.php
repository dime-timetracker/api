<?php

namespace Dime\Server\Console;

use Dime\Server\Config\Loader;
use Illuminate\Database\Capsule\Manager as Capsule;
use Webmozart\Console\Config\DefaultApplicationConfig;


/**
 * Application
 *
 * @author Danilo Kuehn <dk@nogo-software.de>
 */
class Application extends DefaultApplicationConfig
{

    /**
     * @var Loader
     */
    protected $config;

    /**
     * @var Capsule
     */
    protected $database;

    public function __construct(Loader $config, Capsule $database)
    {
        $this->config = $config;
        $this->database = $database;

        parent::__construct('console', '1.0.0');
    }

    protected function configure()
    {
        parent::configure();

        $this
            ->setHelp('Dime Timetracker Configuration Console')
            
            ->beginCommand('database')
                ->setDescription('Database commands')
                ->setHandler(new DatabaseHandler($this->config, $this->database))
                ->beginSubCommand('create')
                    ->setDescription('Create database')
                    ->setHandlerMethod('create')
                ->end()
                ->beginSubCommand('migrate')
                    ->setDescription('Migrate database')
                    ->setHandlerMethod('migrate')
                ->end()
                ->beginSubCommand('seed')
                    ->setDescription('Seed database')
                    ->setHandlerMethod('seed')
                ->end()
            ->end()


            ->beginCommand('user')
                ->setDescription('User commands')
                ->setHandler(new UserHandler())
                ->beginSubCommand('create')
                    ->setDescription('Create new user')
                    ->setHandlerMethod('create')
                ->end()
            ->end()
            ;
    }
}
