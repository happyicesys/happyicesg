<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVendingIdPersonmaintenances extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('personmaintenances', function ($table) {
            $table->integer('vending_id');
            $table->string('lane_number')->nullable();
        });  
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('personmaintenances', function ($table) {
            $table->dropColumn('vending_id');
            $table->dropColumn('lane_number');
        });
    }
}
