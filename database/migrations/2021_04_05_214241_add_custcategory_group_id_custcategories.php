<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCustcategoryGroupIdCustcategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('custcategories', function (Blueprint $table) {
            $table->integer('custcategory_group_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('custcategories', function (Blueprint $table) {
            $table->dropColumn('custcategory_group_id');
        });
    }
}
