<?php

namespace Dime\Api\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20121012091006 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $users = $schema->createTable('users');
        $users->addColumn('id', 'integer', [ 'autoincrement' => true ]);
        $users->addColumn('username', 'string', [ 'length' => 255 ]);
        $users->addColumn('username_canonical', 'string', [ 'length' => 255 ]);
        $users->addColumn('email', 'string', [ 'length' => 255 ]);
        $users->addColumn('email_canonical', 'string', [ 'length' => 255 ]);
        $users->addColumn('enabled', 'boolean');
        $users->addColumn('salt', 'string', [ 'length' => 255 ]);
        $users->addColumn('password', 'string', [ 'length' => 255 ]);
        $users->addColumn('last_login', 'datetime', [ 'notnull' => false ]);
        $users->addColumn('locked', 'boolean');
        $users->addColumn('expired', 'boolean');
        $users->addColumn('expires_at', 'datetime', [ 'notnull' => false ]);
        $users->addColumn('confirmation_token', 'string', [ 'length' => 255 ]);
        $users->addColumn('password_requested_at', 'datetime', [ 'notnull' => false ]);
        $users->addColumn('roles', 'text');
        $users->addColumn('credentials_expired', 'boolean');
        $users->addColumn('credentials_expire_at', 'datetime', [ 'notnull' => false ]);
        $users->addColumn('firstname', 'string', [ 'length' => 255 ]);
        $users->addColumn('lastname', 'string', [ 'length' => 255 ]);
        $users->addColumn('created_at', 'datetime');
        $users->addColumn('updated_at', 'datetime');
        
        $users->setPrimaryKey(array('id'));
        $users->addUniqueIndex(array('username_canonical'), 'UNIQ_1483A5E992FC23A8');
        $users->addUniqueIndex(array('email_canonical'), 'UNIQ_1483A5E9A0D96FBF');
        
        $settings = $schema->createTable('settings');
        $settings->addColumn('id', 'integer', [ 'autoincrement' => true ]);
        $settings->addColumn('user_id', 'integer');
        $settings->addColumn('name', 'string', [ 'length' => 255 ]);
        $settings->addColumn('namespace', 'string', [ 'length' => 255 ]);
        $settings->addColumn('value', 'string', [ 'length' => 255 ]);
        $settings->addColumn('created_at', 'datetime');
        $settings->addColumn('updated_at', 'datetime');
        
        $settings->setPrimaryKey(array('id'));
        $settings->addForeignKeyConstraint($users, ['user_id'], ['id'], ['onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE'], 'FK_E545A0C5A76ED395');
    }

    public function down(Schema $schema)
    {
        $settings = $schema->getTable('settings');
        $settings->removeForeignKey('FK_E545A0C5A76ED395');
        $schema->dropTable('settings');
        
        $users = $schema->getTable('users');
        $users->dropIndex('UNIQ_1483A5E992FC23A8');
        $users->dropIndex('UNIQ_1483A5E9A0D96FBF');
        $schema->dropTable('users');
    }
}
