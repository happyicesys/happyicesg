<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePositionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('positions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('job_title');
            $table->text('duty')->nullable();
            $table->time('work_start');
            $table->time('work_end');
            $table->decimal('work_day', 3, 1);
            $table->string('salary_period');
            $table->decimal('basic', 9, 2);
            $table->string('ot_period');
            $table->decimal('ot_rate', 5, 2);
            $table->string('probation_length');
            $table->text('remark')->nullable();
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
        Schema::drop('positions');
    }
}
