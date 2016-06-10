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
            $table->decimal('cost_rate', 5, 2)->nullable();
            $table->string('active')->default('Yes');
            $table->string('site_name');

            $table->text('note')->nullable();
            $table->string('salutation');
            $table->datetime('dob')->nullable();
            $table->string('cust_type')->nullable();
            $table->integer('parent_id')->nullable()->index();
            $table->string('parent_name')->nullable()->index();
            $table->integer('lft')->nullable()->index();
            $table->integer('rgt')->nullable()->index();
            $table->integer('depth')->nullable();
            $table->integer('user_id')->nullable();
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
