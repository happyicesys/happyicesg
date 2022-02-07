<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePriceTemplateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('price_template_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('item_id');
            $table->integer('price_template_id');
            $table->decimal('retail_price', 10, 2)->nullable();
            $table->decimal('quote_price', 10, 2)->nullable();
            $table->integer('sequence')->nullable();
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
        Schema::drop('price_template_items');
    }
}
