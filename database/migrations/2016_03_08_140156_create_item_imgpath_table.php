<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemImgpathTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('items', function ($table) {
            $table->string('main_imgpath')->nullable();
            $table->string('main_imgcaption')->nullable();
            $table->integer('img_remain')->default(4);
            $table->integer('publish')->default(0);
        });

        Schema::create('image_item', function (Blueprint $table){
            $table->increments('id');
            $table->integer('item_id')->unsigned();
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            $table->string('caption');
            $table->string('path');
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
        Schema::table('items', function($table)
        {
            $table->dropColumn('main_imgpath');
            $table->dropColumn('main_imgcaption');
            $table->dropColumn('img_remain');
            $table->dropColumn('publish');
        });

        Schema::drop('image_item');
    }
}
