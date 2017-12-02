<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFpricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fprices', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('retail_price', 10, 2);
            $table->decimal('quote_price', 10, 2);
            $table->string('created_by');
            $table->string('updated_by');
            $table->timestamps();

            $table->integer('item_id')->unsigned();
            $table->integer('person_id')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fprices');
    }
}
