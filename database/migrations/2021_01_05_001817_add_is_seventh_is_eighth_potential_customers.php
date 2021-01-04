<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsSeventhIsEighthPotentialCustomers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('potential_customers', function (Blueprint $table) {
            $table->boolean('is_seventh')->default(false);
            $table->boolean('is_eighth')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('potential_customers', function (Blueprint $table) {
            $table->dropColumn('is_seventh');
            $table->dropColumn('is_eighth');
        });
    }
}
