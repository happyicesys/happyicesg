<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('items', function ($table) {

            $table->decimal('qty_now', 12, 4);
            $table->decimal('qty_last', 12, 4);

        });


        Schema::create('inventories', function (Blueprint $table) {

            $table->increments('id');
            $table->string('batch_num');
            $table->string('type');
            $table->decimal('qtytotal_current', 12, 4);
            $table->decimal('qtytotal_incoming', 12, 4);
            $table->decimal('qtytotal_after', 12, 4);
            $table->text('remark')->nullable();
            $table->timestamp('rec_date');
            $table->timestamps();

            $table->integer('creator_id')->unsigned();
            $table->string('created_by');
            $table->string('updated_by');

        });

        $statement = "ALTER TABLE inventories AUTO_INCREMENT = 100001;";
        DB::unprepared($statement);

        Schema::create('invrecords', function (Blueprint $table){

            $table->increments('id');
            $table->decimal('qtyrec_current', 12, 4);
            $table->decimal('qtyrec_incoming', 12, 4);
            $table->decimal('qtyrec_after', 12, 4);

            $table->integer('item_id')->unsigned();
            $table->foreign('item_id')->references('id')->on('items');

            $table->integer('inventory_id')->unsigned();
            $table->foreign('inventory_id')->references('id')->on('inventories')->onDelete('cascade');
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
        Schema::drop('invrecords');

        Schema::drop('inventories');

        Schema::table('items', function($table)
        {
            $table->dropColumn('qty_now');
            $table->dropColumn('qty_last');
        });
    }
}
