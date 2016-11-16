<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateD2dOnlineSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('d2d_online_sales', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sequence')->unsigned();
            $table->string('caption');
            $table->integer('qty_divisor')->default(1);
            $table->string('coverage')->default('all');

            $table->integer('item_id')->unsigned()->nullable();
            $table->foreign('item_id')->references('id')->on('items');
            $table->integer('person_id')->unsigned()->nullable();
            $table->foreign('person_id')->references('id')->on('people');
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
        Schema::drop('d2d_online_sales');
    }
}
