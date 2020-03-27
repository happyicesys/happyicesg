<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusSubmissionDateDriverLocations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('driver_locations', function ($table) {
            $table->integer('status')->nullable();
            $table->datetime('submission_date')->nullable();
            $table->integer('updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('driver_locations', function ($table) {
            $table->dropColumn('status');
            $table->dropColumn('submission_date');
            $table->dropColumn('updated_by');
        });
    }
}
