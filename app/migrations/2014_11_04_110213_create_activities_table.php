<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivitiesTable extends Migration
{

    protected $table = 'activities';

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
                $table->string('description');
                $table->decimal('rate');
                $table->enum('rate_reference', ['customer', 'project', 'service']);
                $table->integer('customer_id')->unsigned()->nullable();
                $table->integer('project_id')->unsigned()->nullable();
                $table->integer('service_id')->unsigned()->nullable();
                $table->integer('user_id')->unsigned();
                $table->timestamps();

                $table->foreign('customer_id')->references('id')->on('customers')->onDelete('null');
                $table->foreign('project_id')->references('id')->on('projects')->onDelete('null');
                $table->foreign('service_id')->references('id')->on('services')->onDelete('null');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
