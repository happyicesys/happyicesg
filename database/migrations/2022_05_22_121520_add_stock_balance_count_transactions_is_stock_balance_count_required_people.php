<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStockBalanceCountTransactionsIsStockBalanceCountRequiredPeople extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->integer('stock_balance_count')->nullable();
        });

        Schema::table('people', function (Blueprint $table) {
            $table->boolean('is_stock_balance_count_required')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('stock_balance_count');
        });

        Schema::table('people', function (Blueprint $table) {
            $table->dropColumn('is_stock_balance_count_required');
        });
    }
}
