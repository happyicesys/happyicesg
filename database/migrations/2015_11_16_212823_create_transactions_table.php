<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Carbon\Carbon;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('total', 10, 2);
            $table->text('transremark')->nullable();
            $table->string('person_code')->nullable();
            $table->string('name');
            $table->integer('person_id')->unsigned()->nullable();
            $table->foreign('person_id')->references('id')->on('people');
            $table->timestamp('delivery_date')->nullable();
            $table->timestamp('order_date')->nullable();
            $table->string('driver')->nullable();
            $table->string('status')->default('Pending');
            $table->string('pay_status')->default('Owe');
            $table->text('del_address')->nullable();
            $table->string('po_no')->nullable();
            $table->decimal('total_qty', 12, 4)->nullable();
            $table->string('cancel_trace')->nullable();
            $table->datetime('paid_at')->nullable();
            $table->string('pay_method')->nullable();
            $table->string('note')->nullable();
            $table->timestamps();
            $table->softDeletes();

            //record who paid and updated
            $table->string('updated_by');
            $table->string('paid_by');

            $table->integer('dtdtransaction_id')->unsigned()->nullable();
            $table->string('contact')->nullable();
            $table->string('del_postcode')->nullable();
            $table->decimal('delivery_fee', 10, 2)->nullable();
            $table->text('bill_address')->nullable();
            $table->integer('digital_clock')->nullable();
            $table->integer('analog_clock')->nullable();
            $table->decimal('balance_coin', 10, 2)->nullable();
            $table->boolean('is_freeze')->default(0);
            $table->boolean('is_required_analog')->default(1);
            $table->integer('ftransaction_id')->nullable();
            $table->integer('sales_count')->nullable();
            $table->decimal('sales_amount', 10, 2)->nullable();
        });

        $statement = "ALTER TABLE transactions AUTO_INCREMENT = 160001;";
        DB::unprepared($statement);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('transactions');
    }
}
