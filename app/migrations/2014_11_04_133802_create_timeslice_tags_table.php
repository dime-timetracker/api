<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimesliceTagsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('timeslice_tags', function(Blueprint $table) {
            $table->integer('timeslice_id')->unsigned();
            $table->integer('tag_id')->unsigned();

            $table->foreign('timeslice_id')->references('id')->on('timeslices')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('timeslice_tags', function(Blueprint $table) {
            $table->drop();
        });
	}
}
