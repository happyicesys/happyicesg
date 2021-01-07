<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPotentialCustomerIdPotentialCustomerAttachments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('potential_customer_attachments', function (Blueprint $table) {
            $table->integer('potential_customer_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('potential_customer_attachments', function (Blueprint $table) {
            $table->dropColumn('potential_customer_id');
        });
    }
}
