<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTransactionsAnalogclockDigitalclockBalancecoin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function ($table)
        {
            $table->integer('digital_clock')->nullable();
            $table->integer('analog_clock')->nullable();
            $table->decimal('balance_coin', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function($table){
            $table->dropColumn('digital_clock');
            $table->dropColumn('analog_clock');
            $table->dropColumn('balance_coin');
        });
    }
}
