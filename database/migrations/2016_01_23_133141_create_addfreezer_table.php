a<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddfreezerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addfreezers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('freezerqty')->default(1);
            $table->timestamps();

            $table->integer('freezer_id')->unsigned()->nullable();
            $table->foreign('freezer_id')->references('id')->on('freezers')->onDelete('cascade');

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
        Schema::drop('addfreezers');
    }
}
