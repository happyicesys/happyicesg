<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProductidErrorcodePersonmaintenances extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('personmaintenances', function ($table) {
            $table->string('product_id')->nullable();
            $table->string('error_code')->nullable();
            $table->integer('failuretype_id')->nullable();
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
            $table->dropColumn('product_id');
            $table->dropColumn('error_code');
            $table->dropColumn('failuretype_id');
        });
    }
}
