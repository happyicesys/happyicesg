<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImportTransactionExcelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('import_transaction_excels', function (Blueprint $table) {
            $table->increments('id');
            $table->datetime('upload_date');
            $table->string('file_name');
            $table->string('file_url');
            $table->string('result_url');
            $table->timestamps();

            $table->integer('uploaded_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('import_transaction_excels');
    }
}
