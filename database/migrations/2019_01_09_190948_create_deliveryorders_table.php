<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeliveryordersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deliveryorders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('job_type');
            $table->string('po_no');
            $table->datetime('submission_datetime')->nullable();
            $table->string('brands');
            $table->string('quantities');
            $table->datetime('pickup_date')->nullable();
            $table->string('pickup_timerange');
            $table->string('pickup_attn');
            $table->string('pickup_contact');
            $table->string('pickup_address');
            $table->string('pickup_postcode');
            $table->string('pickup_comment');

            $table->datetime('delivery_date1')->nullable();
            $table->string('delivery_timerange');
            $table->string('delivery_attn');
            $table->string('delivery_contact');
            $table->string('delivery_address');
            $table->string('delivery_postcode');
            $table->string('delivery_comment');

            $table->integer('transaction_id');
            $table->integer('requester');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deliveryorders');
    }
}
