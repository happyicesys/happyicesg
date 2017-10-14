<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPeopleVendingpiecepriceVendingmonthlyrentalVendingprofitsharing extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('people', function ($table)
        {
            $table->decimal('vending_piece_price', 10, 2)->nullable();
            $table->decimal('vending_monthly_rental', 10, 2)->nullable();
            $table->decimal('vending_profit_sharing', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('people', function($table)
        {
            $table->dropColumn('vending_piece_price');
            $table->dropColumn('vending_monthly_rental');
            $table->dropColumn('vending_profit_sharing');
        });
    }
}
