<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddToTransactionIdTransactionpersonassets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactionpersonassets', function ($table) {
            $table->integer('to_transaction_id');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactionpersonassets', function ($table) {
            $table->dropColumn('to_transaction_id');
        });
    }
}
