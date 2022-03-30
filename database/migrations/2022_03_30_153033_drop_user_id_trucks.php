<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropUserIdTrucks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trucks', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->integer('truck_id')->nullable();
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
            $table->dropColumn('truck_id');
        });
    }
}
