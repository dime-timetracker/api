<?php

namespace Dime\Server\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

class Version20121031191344 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $users = $schema->getTable('users');
        
        $services = $schema->createTable('services');
        $services->addColumn('id', 'integer', [ 'autoincrement' => true ]);
        $services->addColumn('user_id', 'integer');
        $services->addColumn('name', 'string', [ 'length' => 255, 'notnull' => false ]);
        $services->addColumn('alias', 'string', [ 'length' => 30 ]);
        $services->addColumn('description', 'text', [ 'notnull' => false ]);
        $services->addColumn('rate', 'decimal', [ 'notnull' => false, 'precision' => 10, 'scale' => 2 ]);
        $services->addColumn('created_at', 'datetime');
        $services->addColumn('updated_at', 'datetime');
        
        $services->setPrimaryKey(array('id'));
        $services->addUniqueIndex(array('alias', 'user_id'), 'unique_service_alias_user');
        $services->addForeignKeyConstraint($users, ['user_id'], ['id'], ['onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE'], 'FK_7332E169A76ED395');
        
        $customers = $schema->createTable('customers');
        $customers->addColumn('id', 'integer', [ 'autoincrement' => true ]);
        $customers->addColumn('user_id', 'integer');
        $customers->addColumn('name', 'string', [ 'length' => 255 ]);
        $customers->addColumn('alias', 'string', [ 'length' => 30 ]);
        $customers->addColumn('created_at', 'datetime');
        $customers->addColumn('updated_at', 'datetime');
        
        $customers->setPrimaryKey(array('id'));
        $customers->addUniqueIndex(array('alias', 'user_id'), 'unique_customer_alias_user');
        $customers->addForeignKeyConstraint($users, ['user_id'], ['id'], ['onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE'], 'FK_62534E21A76ED395');
    }

    public function down(Schema $schema)
    {
        $services = $schema->getTable('services');
        $services->dropIndex('unique_service_alias_user');
        $services->removeForeignKey('FK_7332E169A76ED395');
        $schema->dropTable('services');
        
        $customers = $schema->getTable('customers');
        $customers->dropIndex('unique_customer_alias_user');
        $customers->removeForeignKey('FK_62534E21A76ED395');
        $schema->dropTable('customers');
    }
}
