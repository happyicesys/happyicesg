<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPriceRemarkBomcomponentsBompartsBompartconsumables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bomcomponents', function($table) {
            $table->text('price_remark')->nullable();
        });
        Schema::table('bomparts', function($table) {
            $table->text('price_remark')->nullable();
        });
        Schema::table('bompartconsumables', function($table) {
            $table->text('price_remark')->nullable();
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
            $table->dropColumn('price_remark');
        });
        Schema::table('bomparts', function($table) {
            $table->dropColumn('price_remark');
        });
        Schema::table('bompartconsumables', function($table) {
            $table->dropColumn('price_remark');
        });
    }
}
