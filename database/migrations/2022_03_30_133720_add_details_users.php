<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDetailsUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('nationality_country_id')->nullable();
            $table->integer('birth_country_id')->nullable();
            $table->string('fin_no')->nullable();
            $table->string('permit_no')->nullable();
            $table->datetime('permit_expiry_date')->nullable();
            $table->datetime('dob')->nullable();
            $table->integer('sex_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('nationality_country_id');
            $table->dropColumn('birth_country_id');
            $table->dropColumn('fin_no');
            $table->dropColumn('permit_no');
            $table->dropColumn('permit_expiry_date');
            $table->dropColumn('dob');
            $table->dropColumn('sex_id');
        });
    }
}
