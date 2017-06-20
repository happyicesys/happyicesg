<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddItemsProductpage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('items', function ($table) {
            $table->integer('itemcategory_id')->nullable();
            $table->string('nutri_imgpath')->nullable();
            $table->boolean('is_healthier')->default(0);
            $table->boolean('is_halal')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('items', function($table) {
            $table->dropColumn('itemcategory_id');
            $table->dropColumn('nutri_imgpath');
            $table->dropColumn('is_healthier');
            $table->dropColumn('is_halal');
        });
    }
}
