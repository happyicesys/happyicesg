<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBomcurrencyIdBomcomponentsBompartsBompartconsumables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bomcomponents', function($table) {
            $table->integer('bomcurrency_id')->nullable();
        });
        Schema::table('bomparts', function($table) {
            $table->integer('bomcurrency_id')->nullable();
        });
        Schema::table('bompartconsumables', function($table) {
            $table->integer('bomcurrency_id')->nullable();
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
            $table->dropColumn('bomcurrency_id');
        });
        Schema::table('bomparts', function($table) {
            $table->dropColumn('bomcurrency_id');
        });
        Schema::table('bompartconsumables', function($table) {
            $table->dropColumn('bomcurrency_id');
        });
    }
}
