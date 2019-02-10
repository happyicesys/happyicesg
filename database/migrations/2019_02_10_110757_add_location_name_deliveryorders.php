<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLocationNameDeliveryorders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deliveryorders', function ($table) {
            $table->string('pickup_location_name')->nullable();
            $table->string('delivery_location_name')->nullable();
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
            $table->dropColumn('pickup_location_name');
            $table->dropColumn('delivery_location_name');
        });
    }
}
