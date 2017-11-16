<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLatLngPeople extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('people', function ($table){
            $table->decimal('del_lat', 8, 6)->nullable();
            $table->decimal('del_lng', 9, 6)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('people', function($table){
            $table->dropColumn('del_lat');
            $table->dropColumn('del_lng');
        });
    }
}
