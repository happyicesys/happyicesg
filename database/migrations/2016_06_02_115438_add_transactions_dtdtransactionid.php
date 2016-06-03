<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTransactionsDtdtransactionid extends Migration
{
    public function up()
    {
        Schema::table('transactions', function ($table)
        {
            $table->integer('dtdtransaction_id')->unsigned()->nullable();
        });
    }

    public function down()
    {
        Schema::table('transactions', function($table)
        {
            $table->dropColumn('dtdtransaction_id');
        });
    }
}
