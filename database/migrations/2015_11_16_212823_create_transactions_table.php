<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('total', 10, 2);
            $table->text('transremark')->nullable();
            $table->integer('person_id')->unsigned()->nullable();
            $table->foreign('person_id')->references('id')->on('people');
            $table->timestamp('delivery_date')->nullable();
            $table->string('status');
            $table->string('pay_status');
            $table->timestamps();
            $table->softDeletes();

            //record who created and updated
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
        });

        $statement = "ALTER TABLE transactions AUTO_INCREMENT = 100001;";
        DB::unprepared($statement);         
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('transactions');
    }
}
