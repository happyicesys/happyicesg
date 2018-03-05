<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBompartconsumablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bompartconsumables', function (Blueprint $table) {
            $table->increments('id');
            $table->string('partconsumable_id')->unique();
            $table->string('name');
            $table->text('remark')->nullable();
            $table->integer('bompart_id');
            $table->integer('updated_by');
            $table->integer('qty');
            $table->string('color');
            $table->string('supplier_order');
            $table->decimal('unit_price', 10, 2);
            $table->string('pic')->nullable();
            $table->string('drawing_id')->nullable();
            $table->string('drawing_path')->nullable();
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
        Schema::drop('bompartconsumables');
    }
}
