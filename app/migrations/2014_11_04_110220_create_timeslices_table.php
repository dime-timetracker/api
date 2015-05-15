<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimeslicesTable extends Migration
{

    protected $table = 'timeslices';

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
                $table->integer('duration')->unsigned()->nullable();
                $table->dateTime('started_at');
                $table->dateTime('stopped_at')->nullable();
                $table->integer('activity_id')->unsigned();
                $table->integer('user_id')->unsigned();
                $table->timestamps();

                $table->foreign('activity_id')->references('id')->on('activities');
                $table->foreign('user_id')->references('id')->on('users');
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
