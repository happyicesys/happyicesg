<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRequesterNotificationEmailsDeliveryorders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deliveryorders', function ($table) {
            $table->string('requester_notification_emails')->nullable();
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
            $table->dropColumn('requester_notification_emails');
        });
    }
}
