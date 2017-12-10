<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDtdtransactionsDtddealsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dtdtransactions', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('total', 10, 2);
            $table->decimal('total_qty', 12, 4)->nullable();
            $table->text('transremark')->nullable();
            $table->string('person_code')->nullable();
            $table->string('name');
            $table->integer('person_id')->unsigned()->nullable();
            $table->foreign('person_id')->references('id')->on('people');
            $table->timestamp('delivery_date')->nullable();
            $table->timestamp('order_date')->nullable();
            $table->string('driver')->nullable();
            $table->string('status')->default('Confirmed');
            $table->string('pay_status')->default('Owe');
            $table->text('del_address')->nullable();
            $table->string('po_no')->nullable();
            $table->string('cancel_trace')->nullable();
            $table->string('pay_method')->nullable();
            $table->string('note')->nullable();
            $table->datetime('paid_at')->nullable();
            $table->integer('transaction_id')->nullable();
            $table->timestamps();

            //record who paid and updated
            $table->string('updated_by');
            $table->string('paid_by');
            $table->string('created_by');

            $table->string('contact')->nullable();
            $table->string('del_postcode')->nullable();
            $table->string('type')->nullable();
            $table->decimal('delivery_fee', 10, 2)->nullable;
            $table->text('bill_address')->nullable();
        });

        Schema::create('dtddeals', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('qty', 12, 4);
            $table->decimal('amount', 10, 2);
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->string('qty_status')->nullable();
            $table->string('deal_id')->nullable();
            $table->timestamps();

            $table->integer('item_id')->unsigned()->nullable();
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');

            $table->integer('transaction_id')->unsigned()->nullable();
            $table->foreign('transaction_id')->references('id')->on('dtdtransactions')->onDelete('cascade');

            $table->decimal('dividend', 10, 2)->nullable();
            $table->decimal('divisor', 10, 2)->nullable();
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->boolean('is_freeze')->default(0);
            $table->decimal('qty_before', 12, 4)->nullable();
            $table->decimal('qty_after', 12, 4)->nullable();
        });

        $statement = "ALTER TABLE dtdtransactions AUTO_INCREMENT = 100001;";
        DB::unprepared($statement);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('dtddeals');
        Schema::drop('dtdtransactions');
    }
}
