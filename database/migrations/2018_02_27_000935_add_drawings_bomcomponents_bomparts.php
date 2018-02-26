<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDrawingsBomcomponentsBomparts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bomcomponents', function($table) {
            $table->string('drawing_id')->nullable();
            $table->string('drawing_path')->nullable();
        });

        Schema::table('bomparts', function($table) {
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
        Schema::table('bomcomponents', function($table) {
            $table->dropColumn('drawing_id');
            $table->dropColumn('drawing_path');
        });

        Schema::table('bomparts', function($table) {
            $table->dropColumn('drawing_id');
            $table->dropColumn('drawing_path');
        });
    }
}
