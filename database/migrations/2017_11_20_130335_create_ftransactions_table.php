<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFtransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ftransactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ftransaction_id')->unsigned();
            $table->decimal('total', 10, 2);
            $table->decimal('taxtotal', 10, 2);
            $table->decimal('finaltotal', 10, 2);
            $table->timestamp('collection_datetime')->nullable();
            $table->integer('person_id')->unsigned()->nullable();
            $table->integer('digital_clock')->nullable();
            $table->integer('analog_clock')->nullable();
            $table->integer('sales')->nullable();
            $table->integer('franchisee_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ftransactions');
    }
}
