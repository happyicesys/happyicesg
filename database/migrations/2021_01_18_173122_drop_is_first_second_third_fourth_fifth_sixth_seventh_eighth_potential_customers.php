<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropIsFirstSecondThirdFourthFifthSixthSeventhEighthPotentialCustomers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('potential_customers', function (Blueprint $table) {
            $table->dropColumn('is_first');
            $table->dropColumn('is_second');
            $table->dropColumn('is_third');
            $table->dropColumn('is_fourth');
            $table->dropColumn('is_fifth');
            $table->dropColumn('is_sixth');
            $table->dropColumn('is_seventh');
            $table->dropColumn('is_eighth');
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
            //
        });
    }
}
