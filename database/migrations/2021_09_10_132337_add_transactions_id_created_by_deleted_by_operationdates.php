<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTransactionsIdCreatedByDeletedByOperationdates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('operationdates', function (Blueprint $table) {
            $table->integer('created_by');
            // $table->integer('deleted_by')->nullable();
            // $table->integer('transaction_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('operationdates', function (Blueprint $table) {
            $table->dropColumn('created_by');
            // $table->dropColumn('deleted_by');
            // $table->dropColumn('transaction_id');
        });
    }
}
