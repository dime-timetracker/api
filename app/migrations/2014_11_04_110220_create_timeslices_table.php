<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimeslicesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('timeslices', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('duration')->unsigned()->nullable();
            $table->dateTime('started_at');
            $table->dateTime('stopped_at')->nullable();
            $table->integer('activity_id');
            $table->integer('user_id')->unsigned();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');;
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('timeslices', function(Blueprint $table) {
            $table->drop();
        });
	}
}
