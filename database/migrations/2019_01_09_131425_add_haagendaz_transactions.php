<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHaagendazTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function ($table) {
            $table->integer('created_by');
            $table->boolean('is_deliveryorder')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function ($table) {
            $table->dropColumn('created_by');
            $table->dropColumn('is_deliveryorder');
        });
    }
}
