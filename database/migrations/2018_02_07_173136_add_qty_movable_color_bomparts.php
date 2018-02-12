<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddQtyMovableColorBomparts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bomparts', function($table) {
            $table->integer('qty')->nullable();
            $table->boolean('movable')->default(0);
            $table->string('color')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bomparts', function($table) {
            $table->dropColumn('qty');
            $table->dropColumn('movable');
            $table->dropColumn('color');
        });
    }
}
