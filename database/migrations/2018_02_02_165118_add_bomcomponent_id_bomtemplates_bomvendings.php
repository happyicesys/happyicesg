<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBomcomponentIdBomtemplatesBomvendings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bomtemplates', function($table) {
            $table->integer('bomcomponent_id');
        });

        Schema::table('bomvendings', function($table) {
            $table->integer('bomcomponent_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bomtemplates', function($table) {
            $table->dropColumn('bomcomponent_id');
        });

        Schema::table('bomvendings', function($table) {
            $table->dropColumn('bomcomponent_id');
        });
    }
}
