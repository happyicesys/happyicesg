<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBomcomponentcustcatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bomcomponentcustcats', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('custcategory_id');
            $table->integer('bomcomponent_id');
            $table->integer('updated_by');
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
        Schema::drop('bomcomponentcustcats');
    }
}
