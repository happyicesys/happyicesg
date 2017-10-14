<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMonthlyutilitiesClockeradjustmentPeople extends Migration
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
            $table->decimal('vending_monthly_utilities', 10, 2)->nullable();
            $table->decimal('vending_clocker_adjustment', 10, 2)->nullable();
            $table->boolean('is_profit_sharing_report')->default(0);
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
            $table->dropColumn('vending_monthly_utilities');
            $table->dropColumn('vending_clocker_adjustment');
            $table->dropColumn('is_profit_sharing_report');
        });
    }
}
