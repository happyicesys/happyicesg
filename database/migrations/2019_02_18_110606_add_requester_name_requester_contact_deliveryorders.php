<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRequesterNameRequesterContactDeliveryorders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deliveryorders', function ($table) {
            $table->string('requester_name')->nullable();
            $table->string('requester_contact')->nullable();
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
            $table->dropColumn('requester_name');
            $table->dropColumn('requester_contact');
        });
    }
}
