<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddaccessoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addaccessories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('accessoryqty')->default(1);
            $table->timestamps();

            $table->integer('accessory_id')->unsigned()->nullable();
            $table->foreign('accessory_id')->references('id')->on('accessories')->onDelete('cascade');

            $table->integer('person_id')->unsigned()->nullable();
            $table->foreign('person_id')->references('id')->on('people')->onDelete('cascade');                         
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('addaccessories');
    }
}
