<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCalendarEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calendar_events', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->text('desc')->nullable();
            $table->datetime('start_date');
            $table->datetime('end_date');
            $table->string('background_color')->nullable();
            $table->string('color')->nullable();
            $table->boolean('is_full_day')->default(true);
            $table->integer('working_shift_item_id');
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
        Schema::drop('calendar_events');
    }
}
