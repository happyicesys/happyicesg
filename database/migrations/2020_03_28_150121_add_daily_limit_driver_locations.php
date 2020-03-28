<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDailyLimitDriverLocations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('driver_locations', function ($table) {
            $table->integer('daily_limit')->nullable();
            $table->text('remarks')->nullable();
            $table->integer('approved_by')->nullable();
            $table->datetime('approved_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('driver_locations', function ($table) {
            $table->dropColumn('daily_limit');
            $table->dropColumn('remarks');
            $table->dropColumn('approved_by');
            $table->dropColumn('approved_at');
        });
    }
}
