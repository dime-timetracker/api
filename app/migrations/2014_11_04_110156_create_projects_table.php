<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectsTable extends Migration
{

    protected $table = 'projects';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Capsule::schema()->hasTable($this->table)) {
            Capsule::schema()->create($this->table,function(Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('alias');
                $table->decimal('rate');
                $table->string('description');
                $table->decimal('budget_price');
                $table->integer('budget_time');
                $table->boolean('is_budget_fixed');
                $table->boolean('enabled');
                $table->integer('customer_id')->unsigned();
                $table->integer('user_id')->unsigned();
                $table->timestamps();

                $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        } else {
            Capsule::schema()->dropIfExists('project_tags');
            Capsule::schema()->table($this->table, function(Blueprint $table) {
                $table->removeColumn('started_at');
                $table->removeColumn('stopped_at');
                $table->removeColumn('deadline');
                $table->removeColumn('fixed_price');
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
