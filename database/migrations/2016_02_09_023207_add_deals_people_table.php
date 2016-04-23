<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDealsPeopleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('people', function ($table) {
            $table->text('note')->nullable();
        });

        Schema::table('deals', function ($table) {
            $table->decimal('unit_price', 10, 2)->nullable();
        });

        Schema::table('transactions', function ($table) {
            $table->decimal('total_qty', 12, 4)->nullable();
        });
    }

    public function down()
    {
        Schema::table('people', function($table)
        {
            $table->dropColumn('note');
        });

        Schema::table('deals', function($table)
        {
            $table->dropColumn('unit_price');
        });

        Schema::table('transactions', function($table)
        {
            $table->dropColumn('total_qty');
        });
    }

}
