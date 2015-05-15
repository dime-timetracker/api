<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{

    protected $table = 'users';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Capsule::schema()->hasTable($this->table)) {
            Capsule::schema()->create($this->table, function(Blueprint $table) {
                $table->increments('id');
                $table->string('username')->unique();
                $table->string('email');
                $table->string('password');
                $table->string('salt');
                $table->string('firstname');
                $table->string('lastname');
                $table->boolean('enabled');
                $table->boolean('expired');
                $table->dateTime('expires_at');
                $table->timestamps();
            });
        } else {
            Capsule::schema()->table($this->table, function(Blueprint $table) {
                $table->removeColumn('username_canonical');
                $table->removeColumn('email_canonical');
                $table->removeColumn('locked');
                $table->removeColumn('confirmation_token');
                $table->removeColumn('password_requested_at');
                $table->removeColumn('roles');
                $table->removeColumn('credentials_expired');
                $table->removeColumn('credentials_expire_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Capsule::schema()->dropIfExists($this->table);
    }

}
