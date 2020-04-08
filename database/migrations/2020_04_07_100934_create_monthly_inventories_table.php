<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMonthlyInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monthly_inventories', function (Blueprint $table) {
            $table->increments('id');
            $table->datetime('cutoff_date');
            $table->decimal('qty', 12, 4)->nullable();
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->decimal('closing_value', 10, 2)->nullable();
            $table->timestamps();

            $table->integer('item_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('monthly_inventories');
    }
}
