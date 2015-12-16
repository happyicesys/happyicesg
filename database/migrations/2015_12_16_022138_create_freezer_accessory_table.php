<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFreezerAccessoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('freezers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('accessories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->timestamps();
        });   

        Schema::create('freezer_person', function (Blueprint $table){
            $table->integer('freezer_id')->unsigned()->index();
            $table->foreign('freezer_id')->references('id')->on('freezers');

            $table->integer('person_id')->unsigned()->index();
            $table->foreign('person_id')->references('id')->on('people');

            $table->timestamps();
        });  

        Schema::create('accessory_person', function (Blueprint $table){
            $table->integer('accessory_id')->unsigned()->index();
            $table->foreign('accessory_id')->references('id')->on('accessories');

            $table->integer('person_id')->unsigned()->index();
            $table->foreign('person_id')->references('id')->on('people');

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
        Schema::drop('freezer_person');
        Schema::drop('accessory_person');
        Schema::drop('freezers');
        Schema::drop('accessories');
    }
}
