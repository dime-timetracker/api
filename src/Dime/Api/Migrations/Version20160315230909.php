<?php

namespace Dime\Api\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20160315230909 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $customers = $schema->getTable('customers');
        $customers->addColumn('enabled', 'boolean', [ 'default' => true ]);
        $customers->addColumn('rate', 'decimal', [ 'notnull' => false, 'precision' => 10, 'scale' => 2 ]);
        
        $services = $schema->getTable('services');
        $services->changeColumn('rate', [ 'default' => 0.0 ]);
        $services->addColumn('enabled', 'boolean', [ 'default' => true ]);
        
        $projects = $schema->getTable('projects');
        $projects->dropColumn('started_at');
        $projects->dropColumn('stopped_at');
        $projects->dropColumn('deadline');
        $projects->dropColumn('fixed_price');
        $projects->addColumn('is_budget_fixed', 'boolean', [ 'default' => true ]);
        $projects->addColumn('enabled', 'boolean', [ 'default' => true ]);
        
        $settings = $schema->getTable('settings');
        $settings->dropColumn('namespace');
        
        $tags = $schema->getTable('tags');
        $tags->dropColumn('system');
        $tags->addColumn('enabled', 'boolean', [ 'default' => true ]);
        
        $schema->dropTable('customer_tags');
        $schema->dropTable('service_tags');
        $schema->dropTable('service_tags');
    }

    public function down(Schema $schema)
    {
        $tags = $schema->getTable('tags');
        $tags->addColumn('system', 'boolean');
        $tags->dropColumn('enabled');
        
        $settings = $schema->getTable('settings');
        $settings->addColumn('namespace', 'string', [ 'length' => 255 ]);
        
        $services = $schema->getTable('services');
        $services->dropColumn('enabled');
        $services->changeColumn('rate', [ 'default' => null ]);
        
        $customer = $schema->getTable('customers');
        $customer->dropColumn('rate');
        $customer->dropColumn('enabled');
    }
}
