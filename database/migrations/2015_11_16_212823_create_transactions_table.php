<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Carbon\Carbon;

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
            $table->string('person_code')->nullable();
            $table->integer('person_id')->unsigned()->nullable();
            $table->foreign('person_id')->references('id')->on('people');           
            $table->timestamp('delivery_date')->default(Carbon::now());
            $table->timestamp('order_date')->default(Carbon::now());
            $table->string('driver')->nullable();
            $table->string('status')->default('Pending');
            $table->string('pay_status')->default('Owe');
            $table->timestamps();
            $table->softDeletes();

            //record who paid and updated  
            $table->string('updated_by');
            $table->string('paid_by');
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
