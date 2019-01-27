<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFromhappyiceTohappyiceDeliveryorders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deliveryorders', function ($table) {
            $table->boolean('from_happyice')->default(false);
            $table->boolean('to_happyice')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('deliveryorders', function ($table) {
            $table->dropColumn('from_happyice');
            $table->dropColumn('to_happyice');
        });
    }
}
