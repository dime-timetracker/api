<?php

namespace Dime\Api\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

class Version20130119115755 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $users = $schema->getTable('users');
        $customers = $schema->getTable('customers');
        
        $projects = $schema->createTable('projects');
        $projects->addColumn('id', 'integer', [ 'autoincrement' => true ]);
        $projects->addColumn('customer_id', 'integer', [ 'notnull' => false ]);
        $projects->addColumn('user_id', 'integer');
        $projects->addColumn('name', 'string', [ 'length' => 255, 'notnull' => false ]);
        $projects->addColumn('alias', 'string', [ 'length' => 30 ]);
        $projects->addColumn('started_at', 'datetime', [ 'notnull' => false ]);
        $projects->addColumn('stopped_at', 'datetime', [ 'notnull' => false ]);
        $projects->addColumn('deadline', 'datetime', [ 'notnull' => false ]);
        $projects->addColumn('description', 'text', [ 'notnull' => false ]);
        $projects->addColumn('rate', 'decimal', [ 'notnull' => false, 'precision' => 10, 'scale' => 2 ]);
        $projects->addColumn('budget_price', 'integer', [ 'notnull' => false ]);
        $projects->addColumn('fixed_price', 'integer', [ 'notnull' => false ]);
        $projects->addColumn('budget_time', 'integer', [ 'notnull' => false ]);
        $projects->addColumn('created_at', 'datetime');
        $projects->addColumn('updated_at', 'datetime');
        
        $projects->setPrimaryKey(array('id'));
        $projects->addUniqueIndex(array('alias', 'user_id'), 'unique_project_alias_user');
        $projects->addForeignKeyConstraint($customers, ['customer_id'], ['id'], ['onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE'], 'FK_5C93B3A49395C3F3');
        $projects->addForeignKeyConstraint($users, ['user_id'], ['id'], ['onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE'], 'FK_5C93B3A4A76ED395');
    }

    public function down(Schema $schema)
    {
        $projects = $schema->getTable('projects');
        $projects->dropIndex('unique_project_alias_user');
        $projects->removeForeignKey('FK_5C93B3A49395C3F3');
        $projects->removeForeignKey('FK_5C93B3A4A76ED395');
        $schema->dropTable('projects');
    }
}
