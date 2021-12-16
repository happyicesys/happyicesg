<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCashlessTerminalIdVendings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendings', function (Blueprint $table) {
            $table->integer('cashless_terminal_id')->nullable();
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
            $table->dropColumn('cashless_terminal_id');
        });
    }
}
