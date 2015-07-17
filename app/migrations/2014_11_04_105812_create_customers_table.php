<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomersTable extends Migration
{

    protected $table = 'customers';

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
                $table->string('alias');
                $table->decimal('rate');
                $table->boolean('enabled')->default(true);
                $table->integer('user_id')->unsigned();
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        } else {
            Capsule::schema()->dropIfExists('customer_tags');
            Capsule::schema()->table($this->table, function(Blueprint $table) {
                $table->decimal('rate');
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
