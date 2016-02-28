<?php

namespace Dime\Server\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpgradeCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('dime:upgrade')
            ->setDescription('Upgrade this dime timetracker instance');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
        // TODO run composer update
        // TODO run migrations
    }
}
