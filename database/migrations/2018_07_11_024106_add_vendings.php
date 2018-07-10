<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVendings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendings', function ($table) {
            $table->string('vend_id');
            $table->string('serial_no');
            $table->string('router');
            $table->text('desc');
        });        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vendings', function ($table) {
            $table->dropColumn('vend_id');
            $table->dropColumn('serial_no');
            $table->dropColumn('router');
            $table->dropColumn('desc');
        });
    }
}
