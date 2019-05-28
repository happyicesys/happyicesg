<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddThruWarehouseTransactionpersonassets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactionpersonassets', function (Blueprint $table) {
            $table->boolean('thru_warehouse')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactionpersonassets', function (Blueprint $table) {
            $table->dropColumn('thru_warehouse');
        });
    }
}
