<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStartYearMonthOldSerialNoVendings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendings', function (Blueprint $table) {
            $table->string('start_year_month')->nullable();
            $table->string('old_serial_no')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vendings', function (Blueprint $table) {
            $table->dropColumn('start_year_month');
            $table->dropColumn('old_serial_no');
        });
    }
}
