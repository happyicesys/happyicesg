<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDrawingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drawings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('drawing_id')->nullable();
            $table->string('drawing_path')->nullable();
            $table->integer('bomcategory_id')->nullable();
            $table->integer('bomcomponent_id')->nullable();
            $table->integer('bompart_id')->nullable();
            $table->integer('bompartconsumable_id')->nullable();
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
        Schema::drop('drawings');
    }
}
