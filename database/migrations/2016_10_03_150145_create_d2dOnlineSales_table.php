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
            $table->decimal('unit_price', 12, 2);
            $table->decimal('qty_divisor', 12, 4);
            $table->string('desc')->nullable;

            $table->integer('item_id')->unsigned()->nullable();
            $table->foreign('item_id')->references('id')->on('items');
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
