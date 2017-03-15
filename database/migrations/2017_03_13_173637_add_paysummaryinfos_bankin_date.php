<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPaysummaryinfosBankinDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('paysummaryinfos', function ($table)
        {
            $table->datetime('bankin_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('paysummaryinfos', function($table)
        {
            $table->dropColumn('bankin_date');
        });
    }
}
