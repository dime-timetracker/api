<?php

namespace Dime\Api\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

class Version20130119183902 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $users = $schema->getTable('users');
        $customers = $schema->getTable('customers');
        $projects = $schema->getTable('projects');
        $services = $schema->getTable('services');
        
        $activities = $schema->createTable('activities');
        $activities->addColumn('id', 'integer', [ 'autoincrement' => true ]);
        $activities->addColumn('user_id', 'integer');
        $activities->addColumn('customer_id', 'integer', [ 'notnull' => false ]);
        $activities->addColumn('project_id', 'integer', [ 'notnull' => false ]);
        $activities->addColumn('service_id', 'integer', [ 'notnull' => false ]);
        $activities->addColumn('description', 'text', [ 'notnull' => false ]);
        $activities->addColumn('rate', 'decimal', [ 'notnull' => false, 'precision' => 10, 'scale' => 2 ]);
        $activities->addColumn('rate_reference', 'string', [ 'length' => 255, 'notnull' => false ]);
        $activities->addColumn('created_at', 'datetime');
        $activities->addColumn('updated_at', 'datetime');
        
        $activities->setPrimaryKey(array('id'));
        $activities->addForeignKeyConstraint($customers, ['customer_id'], ['id'], ['onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE'], 'FK_B5F1AFE59395C3F3');
        $activities->addForeignKeyConstraint($projects, ['project_id'], ['id'], ['onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE'], 'FK_B5F1AFE5166D1F9C');
        $activities->addForeignKeyConstraint($services, ['service_id'], ['id'], ['onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE'], 'FK_B5F1AFE5ED5CA9E6');
        $activities->addForeignKeyConstraint($users, ['user_id'], ['id'], ['onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE'], 'FK_B5F1AFE5A76ED395');
        
        $timeslices = $schema->createTable('timeslices');
        $timeslices->addColumn('id', 'integer', [ 'autoincrement' => true ]);
        $timeslices->addColumn('activity_id', 'integer');
        $timeslices->addColumn('user_id', 'integer');
        $timeslices->addColumn('duration', 'integer');
        $timeslices->addColumn('started_at', 'datetime', [ 'notnull' => false ]);
        $timeslices->addColumn('stopped_at', 'datetime', [ 'notnull' => false ]);
        $timeslices->addColumn('created_at', 'datetime');
        $timeslices->addColumn('updated_at', 'datetime');
        
        $timeslices->setPrimaryKey(array('id'));
        $timeslices->addForeignKeyConstraint($activities, ['activity_id'], ['id'], ['onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE'], 'FK_72C53BF481C06096');
        $timeslices->addForeignKeyConstraint($users, ['user_id'], ['id'], ['onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE'], 'FK_72C53BF4A76ED395');
        
        $this->addSql($schema->toSql($this->connection->getDatabasePlatform()));
    }

    public function down(Schema $schema)
    {
        $timeslices = $schema->getTable('timeslices');
        $timeslices->removeForeignKey('FK_72C53BF481C06096');
        $timeslices->removeForeignKey('FK_72C53BF4A76ED395');
        $schema->dropTable('timeslices');
        
        $activities = $schema->getTable('activities');
        $activities->removeForeignKey('FK_B5F1AFE59395C3F3');
        $activities->removeForeignKey('FK_B5F1AFE5166D1F9C');
        $activities->removeForeignKey('FK_B5F1AFE5ED5CA9E6');
        $activities->removeForeignKey('FK_B5F1AFE5A76ED395');
        $schema->dropTable('activities');
        
        $this->addSql($schema->toDropSql($this->connection->getDatabasePlatform()));
    }
}
