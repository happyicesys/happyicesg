<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostcodesTable extends Migration
{

    public function up()
    {
        Schema::create('postcodes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('value')->unique();
            $table->string('block')->nullable();
            $table->string('area_code')->nullable();
            $table->string('area_name')->nullable();
            $table->string('group')->nullable();
            $table->integer('person_id')->unsigned()->nullable();
            $table->foreign('person_id')->references('id')->on('people');
            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::drop('postcodes');
    }
}
