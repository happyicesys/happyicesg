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
            $table->string('cust_id')->unique();
            $table->string('company');
            $table->string('com_remark')->nullable();
            $table->string('name');
            $table->string('contact');
            $table->string('alt_contact');
            $table->string('bill_address');
            $table->text('del_address');
            $table->string('del_postcode');
            $table->string('email')->nullable();
            $table->string('payterm');
            $table->text('remark')->nullable();
            $table->integer('cost_rate')->nullable();
            $table->string('active')->default('Yes');
            $table->string('site_name');
            $table->timestamps();
            $table->softDeletes()->nullable();

            // recording which profile person belongs
            $table->integer('profile_id')->unsigned();
            $table->foreign('profile_id')->references('id')->on('profiles'); 
        });        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::drop('people');
        // DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
