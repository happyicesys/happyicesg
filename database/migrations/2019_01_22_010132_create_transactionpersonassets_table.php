<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionpersonassetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactionpersonassets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('qty');
            $table->string('serial_no')->nullable();
            $table->text('sticker')->nullable();
            $table->timestamps();

            $table->integer('transaction_id');
            $table->integer('personasset_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactionpersonassets');
    }
}
