<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPostcodeNameContactTransactionDtdtransaction extends Migration
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
            $table->string('contact')->nullable();
            $table->string('del_postcode')->nullable();
        });

        Schema::table('dtdtransactions', function ($table)
        {
            $table->string('contact')->nullable();
            $table->string('del_postcode')->nullable();
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
            $table->dropColumn('contact');
            $table->dropColumn('del_postcode');
        });

        Schema::table('dtdtransactions', function($table)
        {
            $table->dropColumn('contact');
            $table->dropColumn('del_postcode');
        });
    }
}
