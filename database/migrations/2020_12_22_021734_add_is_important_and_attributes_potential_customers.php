<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsImportantAndAttributesPotentialCustomers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('potential_customers', function ($table) {
            $table->boolean('is_important')->default(false);
            $table->boolean('is_first')->default(false);
            $table->boolean('is_second')->default(false);
            $table->boolean('is_third')->default(false);
            $table->boolean('is_fourth')->default(false);
            $table->boolean('is_fifth')->default(false);
            $table->boolean('is_sixth')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('potential_customers', function ($table) {
            $table->dropColumn('is_important');
            $table->dropColumn('is_first');
            $table->dropColumn('is_second');
            $table->dropColumn('is_third');
            $table->dropColumn('is_fourth');
            $table->dropColumn('is_fifth');
            $table->dropColumn('is_sixth');
        });
    }
}
