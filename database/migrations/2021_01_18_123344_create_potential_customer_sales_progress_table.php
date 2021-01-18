<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePotentialCustomerSalesProgressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('potential_customer_sales_progress', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('potential_customer_id');
            $table->integer('sales_progress_id');
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
        Schema::drop('potential_customer_sales_progress');
    }
}
