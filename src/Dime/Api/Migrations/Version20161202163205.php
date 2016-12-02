<?php

namespace Dime\Api\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20161202163205 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $settings = $schema->getTable('settings');
        $settings->changeColumn('value', [ 'length' => 10000 ]);
    }

    public function down(Schema $schema)
    {
        $settings = $schema->getTable('settings');
        $settings->changeColumn('value', [ 'length' => 255 ]);
    }
}
