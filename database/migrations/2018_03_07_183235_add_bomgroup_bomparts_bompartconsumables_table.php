<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBomgroupBompartsBompartconsumablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bomparts', function($table) {
            $table->integer('bomgroup_id')->nullable();
        });
        Schema::table('bompartconsumables', function($table) {
            $table->integer('bomgroup_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bomparts', function($table) {
            $table->dropColumn('bomgroup_id');
        });
        Schema::table('bompartconsumables', function($table) {
            $table->dropColumn('bomgroup_id');
        });
    }
}
