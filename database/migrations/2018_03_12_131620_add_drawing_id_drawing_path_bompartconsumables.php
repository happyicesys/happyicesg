<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDrawingIdDrawingPathBompartconsumables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bompartconsumables', function($table) {
            $table->string('drawing_id')->nullable();
            $table->string('drawing_path')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bompartconsumables', function($table) {
            $table->dropColumn('drawing_id');
            $table->dropColumn('drawing_path');
        });
    }
}
