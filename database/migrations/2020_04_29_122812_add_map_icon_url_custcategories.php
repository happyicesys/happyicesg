<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMapIconUrlCustcategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('custcategories', function ($table) {
            $table->string('map_icon_file')->default('red');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('custcategories', function ($table) {
            $table->dropColumn('map_icon_file');
        });
    }
}
