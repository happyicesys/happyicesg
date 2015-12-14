<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePeopleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('people', function (Blueprint $table) {
              
            $table->increments('id');  
            $table->string('cust_id');
            $table->string('company');
            $table->string('name');
            $table->string('contact');
            $table->string('alt_contact');
            $table->string('bill_address');
            $table->text('del_address');
            $table->string('bill_postcode');
            $table->string('del_postcode');
            $table->string('email')->unique()->nullable();
            $table->string('payterm');
            $table->text('remark')->nullable();
            $table->integer('cost_rate')->nullable();
            $table->timestamps();
            $table->softDeletes();

            //record who created and updated
            /*$table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');*/ 
        });        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('people');
    }
}
