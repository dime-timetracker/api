<?php

namespace Dime\Server\Command;

use Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallCommand extends Command
{

    protected function configure()
    {
        $this
                ->setName('dime:install')
                ->setDescription('Install this dime timetracker instance');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $infoCommand = new \Doctrine\DBAL\Migrations\Tools\Console\Command\StatusCommand();
        $infoCommand->setApplication($this->getApplication());
        $infoCommand->execute(new ArrayInput(['--configuration' => false], $infoCommand->getDefinition()), $output);

        // Migrate
        $migrateArguments = [
            '--configuration' => false,
            '--query-time' => false
        ];
                
        $migrateCommand = new MigrateCommand();
        $migrateCommand->setApplication($this->getApplication());
        $migrateCommand->execute(
            new ArrayInput($migrateArguments, $migrateCommand->getDefinition()), 
            $output
        );
    }

}
