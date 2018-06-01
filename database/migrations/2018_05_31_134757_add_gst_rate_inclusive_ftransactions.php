<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGstRateInclusiveFtransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ftransactions', function ($table) {
            $table->boolean('gst')->default(0);
            $table->boolean('is_gst_inclusive')->default(0);
            $table->decimal('gst_rate', 5, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ftransactions', function ($table) {
            $table->dropColumn('gst');
            $table->dropColumn('is_gst_inclusive');
            $table->dropColumn('gst_rate');
        });
    }
}
