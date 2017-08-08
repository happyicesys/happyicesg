<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsFreezeTransactions extends Migration
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
            $table->boolean('is_freeze')->default(0);
        });
        Schema::table('dtdtransactions', function ($table)
        {
            $table->boolean('is_freeze')->default(0);
        });
        Schema::table('generalsettings', function ($table)
        {
            $table->datetime('INVOICE_FREEZE_DATE')->nullable();
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
            $table->dropColumn('is_freeze');
        });
        Schema::table('dtdtransactions', function($table)
        {
            $table->dropColumn('is_freeze');
        });
        Schema::table('generalsettings', function($table)
        {
            $table->dropColumn('INVOICE_FREEZE_DATE');
        });
    }
}
