<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBeforeQtyAfterQtyDeals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deals', function ($table)
        {
            $table->decimal('qty_before', 12, 4)->nullable();
            $table->decimal('qty_after', 12, 4)->nullable();
        });

        Schema::table('dtddeals', function ($table)
        {
            $table->decimal('qty_before', 12, 4)->nullable();
            $table->decimal('qty_after', 12, 4)->nullable();
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
            $table->dropColumn('qty_before');
            $table->dropColumn('qty_after');
        });
        Schema::table('dtddeals', function($table)
        {
            $table->dropColumn('qty_before');
            $table->dropColumn('qty_after');
        });
    }
}
