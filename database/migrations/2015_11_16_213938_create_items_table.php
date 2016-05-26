<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('product_id')->unique;
            $table->string('name')->unique();
            $table->text('remark')->nullable();
            $table->string('unit');
            $table->string('main_imgpath')->nullable();
            $table->string('main_imgcaption')->nullable();
            $table->integer('img_remain')->default(4);
            $table->integer('publish')->default(0);
            $table->decimal('lowest_limit', 12, 4)->nullable();
            $table->decimal('email_limit', 12, 4)->nullable();
            $table->decimal('qty_order', 12, 4)->nullable();
            $table->boolean('emailed');
            $table->timestamps();
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
        Schema::drop('image_item');
        Schema::drop('items');
    }
}
