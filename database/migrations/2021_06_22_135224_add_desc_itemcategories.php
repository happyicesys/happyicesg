<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDescItemcategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('itemcategories', function (Blueprint $table) {
            $table->text('desc')->nullable();
            $table->string('code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('itemcategories', function (Blueprint $table) {
            $table->dropColumn('desc');
            $table->dropColumn('code');
        });
    }
}
