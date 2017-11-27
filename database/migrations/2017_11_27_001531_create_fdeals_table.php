<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFdealsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fdeals', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('qty', 12, 4);
            $table->decimal('amount', 10, 2);
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->string('qty_status')->nullable();
            $table->string('deal_id')->nullable();
            $table->decimal('dividend', 10, 2)->nullable();
            $table->decimal('divisor', 10, 2)->nullable();
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->timestamps();

            $table->integer('item_id')->unsigned()->nullable();
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');

            $table->integer('ftransaction_id')->unsigned()->nullable();
            $table->foreign('ftransaction_id')->references('id')->on('ftransactions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fdeals');
    }
}
