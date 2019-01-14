<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePersonassetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personassets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->string('name');
            $table->string('brand')->nullable();
            $table->string('size1')->nullable();
            $table->string('size2')->nullable();
            $table->string('weight')->nullable();
            $table->string('capacity')->nullable();
            $table->text('specs1')->nullable();
            $table->text('specs2')->nullable();
            $table->text('specs3')->nullable();

            $table->integer('person_id');
            $table->integer('created_by');
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
        Schema::dropIfExists('personassets');
    }
}
