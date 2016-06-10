<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotifyManagerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifymanagers', function (Blueprint $table) {

            $table->increments('id');
            $table->string('title');
            $table->text('content')->nullable();
            $table->timestamps();

            $table->integer('person_id')->unsigned();
            $table->foreign('person_id')->references('id')->on('people');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('notifymanagers');
    }
}
