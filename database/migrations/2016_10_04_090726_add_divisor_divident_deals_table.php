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
            $table->decimal('dividend', 10, 2)->nullable();
            $table->decimal('divisor', 10, 2)->nullable();
        });

        Schema::table('dtddeals', function($table) {
            $table->decimal('dividend', 10, 2)->nullable();
            $table->decimal('divisor', 10, 2)->nullable();
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
