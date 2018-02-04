<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBommaintenancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bommaintenances', function (Blueprint $table) {
            $table->increments('id');
            $table->string('maintenance_id');
            $table->integer('person_id');
            $table->timestamp('datetime')->nullable();
            $table->integer('technician_id');
            $table->string('urgency')->nullable();
            $table->string('time_spend')->nullable();
            $table->integer('bomcomponent_id');
            $table->string('issue_type');
            $table->string('solution');
            $table->text('remark')->nullable();
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('bommaintenances');
    }
}
