<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsRefundRefundNameRefundBankRefundContactPersonmaintenances extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('personmaintenances', function ($table) {
            $table->boolean('is_refund')->default(0);
            $table->string('refund_name')->nullable();
            $table->string('refund_bank')->nullable();
            $table->string('refund_account')->nullable();
            $table->string('refund_contact')->nullable();
            $table->integer('created_by')->nullable();
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
            $table->dropColumn('is_refund');
            $table->dropColumn('refund_name');
            $table->dropColumn('refund_bank');
            $table->dropColumn('refund_account');
            $table->dropColumn('refund_contact');
            $table->dropColumn('created_by');
        });
    }
}
