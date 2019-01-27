<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDateinDateoutIswarehouseTransactionpersonassets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactionpersonassets', function ($table) {
            $table->boolean('is_warehouse')->default(false);
            $table->datetime('datein')->nullable();
            $table->datetime('dateout')->nullable();
            $table->text('remarks')->nullable();
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
            $table->dropColumn('is_warehouse');
            $table->dropColumn('datein');
            $table->dropColumn('dateout');
            $table->dropColumn('remarks');
        });
    }
}
