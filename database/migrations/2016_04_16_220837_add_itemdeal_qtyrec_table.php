<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddItemdealQtyrecTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deals', function ($table) {
            $table->string('qty_status')->nullable();
        });

        Schema::table('items', function ($table) {
            $table->decimal('lowest_limit', 10, 4)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('deals', function($table)
        {
            $table->dropColumn('qty_status');
        });

        Schema::table('items', function($table)
        {
            $table->dropColumn('lowest_limit');
        });
    }
}
