<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkingShiftItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('working_shift_items', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('is_every_week')->default(true);
            $table->boolean('is_one_time')->default(false);
            $table->string('label');
            $table->integer('day_number');
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
        Schema::drop('working_shift_items');
    }
}
