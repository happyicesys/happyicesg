<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUpdatedPersonVendings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendings', function ($table) {
            $table->integer('updated_by');
            $table->integer('person_id');
        }); 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vendings', function ($table) {
            $table->dropColumn('updated_by');
            $table->dropColumn('person_id');
        });
    }
}
