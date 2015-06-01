<?php

namespace Dime\Server\Console;

use Dime\Server\Config\Loader;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Filesystem\Filesystem;
use Webmozart\Console\Api\Args\Args;
use Webmozart\Console\Api\Command\Command;
use Webmozart\Console\Api\IO\IO;

/**
 * MigrateHandler
 *
 * @author Danilo Kuehn <dk@nogo-software.de>
 */
class DatabaseHandler
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
    }

    public function migrate(Args $args, IO $io, Command $command)
    {
        $repository = new DatabaseMigrationRepository($this->database->getDatabaseManager(), 'migrations');
        if (!$repository->repositoryExists()) {
            $repository->createRepository();
            $io->writeLine('Create database migration table');
        }

        $migrator = new Migrator(
            $repository,
            $this->database->getDatabaseManager(),
            new Filesystem()
        );


        $io->writeLine('Start migration...');
        $migrator->run($this->config['migration_dir']);
        $notes = $migrator->getNotes();
        foreach ($notes as $node) {
            $io->writeLine($node);
        }
        $io->writeLine('Finish migration.');
    }

    public function seed(Args $args, IO $io, Command $command)
    {
        $seeds_path = $this->config['seed_dir'];

        $fs = new Filesystem();
        $files = $fs->glob($seeds_path . '/*_*.php');

		if ($files !== false) {

            $files = array_map(function($file) {
                return str_replace('.php', '', basename($file));
            }, $files);
            sort($files);

            $io->writeLine('Start seeding...');

            foreach ($files as $file) {
                $fs->requireOnce($seeds_path . '/' . $file . '.php');

                $class = implode('_', array_slice(explode('_', $file), 4));

                /**
                 * @var Seeder $seeder
                 */
                $seeder = new $class;
                $io->writeLine('Seed ' . $class);
                $seeder->run();
            }
        }
    }
}
