#!!/usr/bin/env php
<?php
define('ROOT_DIR', realpath(__DIR__));
$loader = require_once ROOT_DIR . '/vendor/autoload.php';

// Configuration
$configuration = [];
if (file_exists(ROOT_DIR . '/config/parameters.php')) {
    $configuration = require_once ROOT_DIR . '/config/parameters.php';
}

$connection = \Doctrine\DBAL\DriverManager::getConnection($configuration['database'], new \Doctrine\DBAL\Configuration());
$platform = $connection->getDatabasePlatform();
$platform->registerDoctrineTypeMapping('enum', 'string');

$configuration = new \Doctrine\DBAL\Migrations\Configuration\Configuration($connection);
$configuration->setMigrationsNamespace('Dime\Server\Migrations');
$configuration->setMigrationsDirectory(ROOT_DIR . '/src/Dime/Api/Migrations');
$configuration->setMigrationsTableName('migration_versions');
$helpers = new Symfony\Component\Console\Helper\HelperSet([
    'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($connection),
    'dialog' => new Symfony\Component\Console\Helper\QuestionHelper(),
    'configuration' => new \Doctrine\DBAL\Migrations\Tools\Console\Helper\ConfigurationHelper($connection, $configuration)
]);

$cli = new \Symfony\Component\Console\Application('Dime Timetracker');
$cli->setCatchExceptions(true);
$cli->setHelperSet($helpers);
$cli->addCommands([
    new Dime\Server\Command\InstallCommand()
]);
$cli->run();
