<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePotentialCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('potential_customers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('custcategory_id')->nullable();
            $table->integer('account_manager_id')->nullable();
            $table->string('attn_to')->nullable();
            $table->string('contact')->nullable();
            $table->text('address')->nullable();
            $table->string('postcode')->nullable();
            $table->text('remarks')->nullable();

            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
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
        Schema::drop('potential_customers');
    }
}
