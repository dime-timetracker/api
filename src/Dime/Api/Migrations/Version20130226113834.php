<?php

namespace Dime\Api\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130226113834 extends AbstractMigration
{
    public function up(Schema $schema)
    {        
        $tags = $schema->createTable('tags');
        $tags->addColumn('id', 'integer', [ 'autoincrement' => true ]);
        $tags->addColumn('user_id', 'integer');
        $tags->addColumn('name', 'string', [ 'length' => 255 ]);
        $tags->addColumn('system', 'boolean', [ 'default' => false ]);
        $tags->addColumn('created_at', 'datetime');
        $tags->addColumn('updated_at', 'datetime');
        
        $tags->setPrimaryKey(array('id'));
        $tags->addUniqueIndex(['name', 'user_id'], 'unique_tag_name_user');
        $tags->addForeignKeyConstraint($schema->getTable('users'), ['user_id'], ['id'], ['onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE'], 'FK_6FBC9426A76ED395');
        
        $activityTags = $schema->createTable('activity_tags');
        $activityTags->addColumn('activity_id', 'integer');
        $activityTags->addColumn('tag_id', 'integer');
        $activityTags->setPrimaryKey(array('activity_id', 'tag_id'));
        $activityTags->addForeignKeyConstraint($schema->getTable('activities'), ['activity_id'], ['id'], ['onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE'], 'FK_6C784FB481C06096');
        $activityTags->addForeignKeyConstraint($tags, ['tag_id'], ['id'], ['onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE'], 'FK_6C784FB4BAD26311');
        
        $customerTags = $schema->createTable('customer_tags');
        $customerTags->addColumn('customer_id', 'integer');
        $customerTags->addColumn('tag_id', 'integer');
        $customerTags->setPrimaryKey(array('customer_id', 'tag_id'));
        $customerTags->addForeignKeyConstraint($schema->getTable('customers'), ['customer_id'], ['id'], ['onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE'], 'FK_3B2D30519395C3F3');
        $customerTags->addForeignKeyConstraint($tags, ['tag_id'], ['id'], ['onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE'], 'FK_3B2D3051BAD26311');
        
        $projectTags = $schema->createTable('project_tags');
        $projectTags->addColumn('project_id', 'integer');
        $projectTags->addColumn('tag_id', 'integer');
        $projectTags->setPrimaryKey(array('project_id', 'tag_id'));
        $projectTags->addForeignKeyConstraint($schema->getTable('projects'), ['project_id'], ['id'], ['onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE'], 'FK_562D5C3E166D1F9C');
        $projectTags->addForeignKeyConstraint($tags, ['tag_id'], ['id'], ['onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE'], 'FK_562D5C3EBAD26311');
        
        $serviceTags = $schema->createTable('service_tags');
        $serviceTags->addColumn('service_id', 'integer');
        $serviceTags->addColumn('tag_id', 'integer');
        $serviceTags->setPrimaryKey(array('service_id', 'tag_id'));
        $serviceTags->addForeignKeyConstraint($schema->getTable('services'), ['service_id'], ['id'], ['onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE'], 'FK_A1FF20CAED5CA9E6');
        $serviceTags->addForeignKeyConstraint($tags, ['tag_id'], ['id'], ['onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE'], 'FK_A1FF20CABAD26311');
        
        $timesliceTags = $schema->createTable('timeslice_tags');
        $timesliceTags->addColumn('timeslice_id', 'integer');
        $timesliceTags->addColumn('tag_id', 'integer');
        $timesliceTags->setPrimaryKey(array('timeslice_id', 'tag_id'));
        $timesliceTags->addForeignKeyConstraint($schema->getTable('timeslices'), ['timeslice_id'], ['id'], ['onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE'], 'FK_4231EEB94FB5678C');
        $timesliceTags->addForeignKeyConstraint($tags, ['tag_id'], ['id'], ['onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE'], 'FK_4231EEB9BAD26311');
    }

    public function down(Schema $schema)
    {
        $timesliceTags = $schema->getTable('timeslice_tags');
        $timesliceTags->removeForeignKey('FK_4231EEB9BAD26311');
        $timesliceTags->removeForeignKey('FK_4231EEB94FB5678C');
        $schema->dropTable('timeslice_tags');
        
        $serviceTags = $schema->getTable('service_tags');
        $serviceTags->removeForeignKey('FK_A1FF20CABAD26311');
        $serviceTags->removeForeignKey('FK_A1FF20CAED5CA9E6');
        $schema->dropTable('service_tags');
        
        $projectTags = $schema->getTable('project_tags');
        $projectTags->removeForeignKey('FK_562D5C3EBAD26311');
        $projectTags->removeForeignKey('FK_562D5C3E166D1F9C');
        $schema->dropTable('project_tags');
        
        $customerTags = $schema->getTable('customer_tags');
        $customerTags->removeForeignKey('FK_3B2D3051BAD26311');
        $customerTags->removeForeignKey('FK_3B2D30519395C3F3');
        $schema->dropTable('customer_tags');
        
        $activityTags = $schema->getTable('activity_tags');
        $activityTags->removeForeignKey('FK_6C784FB4BAD26311');
        $activityTags->removeForeignKey('FK_6C784FB481C06096');
        $schema->dropTable('activity_tags');
        
        $tags = $schema->getTable('tags');
        $tags->dropIndex('unique_tag_name_user');
        $tags->removeForeignKey('FK_6FBC9426A76ED395');
        $schema->dropTable('tags');
        
    }
}
