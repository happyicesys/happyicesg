<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDivisorDividentDealsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deals', function($table) {
            $table->integer('dividend')->nullable();
            $table->integer('divisor')->nullable();
        });

        Schema::table('dtddeals', function($table) {
            $table->integer('dividend')->nullable();
            $table->integer('divisor')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('deals', function($table) {
            $table->dropColumn('dividend');
            $table->dropColumn('divisor');
        });

        Schema::table('dtddeals', function($table) {
            $table->dropColumn('dividend');
            $table->dropColumn('divisor');
        });
    }
}
