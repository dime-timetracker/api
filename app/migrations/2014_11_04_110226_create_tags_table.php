<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTagsTable extends Migration
{

    protected $table = 'tags';

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
                $table->string('name');
                $table->boolean('enabled')->default(true);
                $table->integer('user_id')->unsigned();
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        } else {
            Capsule::schema()->table($this->table, function(Blueprint $table) {
                $table->removeColumn('system');
                $table->boolean('enabled')->default(true);
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
