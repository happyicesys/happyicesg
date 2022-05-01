<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAttachment1Attachment2ServiceItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_items', function (Blueprint $table) {
            $table->integer('attachment1')->nullable();
            $table->integer('attachment2')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_items', function (Blueprint $table) {
            $table->dropColumn('attachment1');
            $table->dropColumn('attachment2');
        });
    }
}
