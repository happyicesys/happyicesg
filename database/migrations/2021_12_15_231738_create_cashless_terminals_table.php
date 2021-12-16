<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCashlessTerminalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cashless_terminals', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('provider_id');
            $table->string('provider_name');
            $table->string('terminal_id');
            $table->datetime('start_date')->nullable();
            $table->integer('vending_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('cashless_terminals');
    }
}
