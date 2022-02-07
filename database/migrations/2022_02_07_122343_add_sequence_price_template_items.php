<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSequencePriceTemplateItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('price_template_items', function (Blueprint $table) {
            $table->integer('sequence')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('price_template_items', function (Blueprint $table) {
            $table->dropColumn('sequence');
        });
    }
}
