<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGeneralsettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('generalsettings', function (Blueprint $table) {

            $table->increments('id');
            $table->text('DTDCUST_EMAIL_CONTENT')->nullable();
            $table->timestamps();

            $table->datetime('INVOICE_FREEZE_DATE')->nullable();
            $table->string('internal_billing_prefix')->default('IN');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('generalsettings');
    }
}
