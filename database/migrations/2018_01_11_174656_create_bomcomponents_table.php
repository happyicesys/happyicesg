<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBomcomponentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bomcomponents', function (Blueprint $table) {
            $table->increments('id');
            $table->string('component_id')->unique();
            $table->string('name');
            $table->text('remark')->nullable();
            $table->integer('bomcategory_id');
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
        Schema::drop('bomcomponents');
    }
}
