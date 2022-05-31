<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemUomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_uoms', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('item_id');
            $table->integer('uom_id');
            $table->boolean('is_base_unit')->default(false);
            $table->boolean('is_transacted_unit')->default(false);
            $table->integer('value')->default(1);
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
        Schema::drop('item_uoms');
    }
}
