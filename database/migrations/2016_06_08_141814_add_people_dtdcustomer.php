<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPeopleDtdcustomer extends Migration
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
            $table->string('block')->nullable();
            $table->string('floor')->nullable();
            $table->string('unit')->nullable();
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
            $table->dropColumn('block');
            $table->dropColumn('floor');
            $table->dropColumn('unit');
        });
    }
}
