<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterOutletVisits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('outlet_visits', function (Blueprint $table) {
            $table->bigInteger('created_by')->index();
            $table->datetime('date')->index();
            $table->bigInteger('person_id')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('outlet_visits', function (Blueprint $table) {
            //
        });
    }
}
