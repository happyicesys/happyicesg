<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBillAddressTransactions extends Migration
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
            $table->text('bill_address')->nullable();
        });

        Schema::table('dtdtransactions', function ($table)
        {
            $table->text('bill_address')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function($table)
        {
            $table->dropColumn('bill_address');
        });
        Schema::table('dtdtransactions', function($table)
        {
            $table->dropColumn('bill_address');
        });
    }
}
