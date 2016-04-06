<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOnlinePriceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('onlinePrices', function (Blueprint $table) {

            $table->increments('id');
            $table->decimal('retail_price', 10, 2);
            $table->integer('divisor');
            $table->text('remark')->nullable();
            $table->timestamps();

            $table->integer('item_id')->unsigned();
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');

            $table->integer('person_id')->unsigned();
            $table->foreign('person_id')->references('id')->on('people')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('onlinePrices');
    }
}
