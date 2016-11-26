<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDtdtransactionsDeliveryFee extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dtdtransactions', function ($table){
            $table->decimal('delivery_fee', 10, 2)->nullable;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dtdtransactions', function($table){
            $table->dropColumn('delivery_fee');
        });
    }
}
