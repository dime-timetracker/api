<?php

namespace Dime\Server\Migrations;

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

        $timeslices = $schema->getTable('timeslices');
        $timeslices->dropColumn('created_at');
        $timeslices->dropColumn('updated_at');

        if ($schema->hasTable('customer_tags')) {
            $schema->dropTable('customer_tags');
        }
        if ($schema->hasTable('service_tags')) {
            $schema->dropTable('service_tags');
        }
        if ($schema->hasTable('service_tags')) {
            $schema->dropTable('service_tags');
        }

        $users = $schema->getTable('users');
        $users->dropColumn('username_canonical');
        $users->dropColumn('email_canonical');
        $users->dropColumn('locked');
        $users->dropColumn('expired');
        $users->dropColumn('expires_at');
        $users->dropColumn('confirmation_token');
        $users->dropColumn('password_requested_at');
        $users->dropColumn('roles');
        $users->dropColumn('credentials_expired');
        $users->dropColumn('credentials_expire_at');

        $access = $schema->createTable('access');
        $access->addColumn('user_id', 'integer');
        $access->addColumn('client', 'string', [ 'length' => 255 ]);
        $access->addColumn('token', 'string', [ 'length' => 255 ]);
        $access->addColumn('created_at', 'datetime');
        $access->addColumn('updated_at', 'datetime');
        $access->addUniqueIndex(['user_id', 'client'], 'UQ_ACCESS');
        $access->addForeignKeyConstraint($users, ['user_id'], ['id'], ['onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE'], 'FK_ACCESS_01');
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

        $schema->dropTable('access');

        $users = $schema->createTable('users');
        $users->addColumn('username_canonical', 'string', [ 'length' => 255 ]);
        $users->addColumn('email_canonical', 'string', [ 'length' => 255 ]);
        $users->addColumn('locked', 'boolean');
        $users->addColumn('expired', 'boolean');
        $users->addColumn('expires_at', 'datetime', [ 'notnull' => false ]);
        $users->addColumn('confirmation_token', 'string', [ 'length' => 255 ]);
        $users->addColumn('password_requested_at', 'datetime', [ 'notnull' => false ]);
        $users->addColumn('roles', 'text');
        $users->addColumn('credentials_expired', 'boolean');
        $users->addColumn('credentials_expire_at', 'datetime', [ 'notnull' => false ]);

    }
}
