<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFtransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ftransactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ftransaction_id')->unsigned();
            $table->decimal('total', 10, 2);
            $table->timestamp('delivery_date')->nullable();
            $table->string('status')->default('Pending');
            $table->text('transremark')->nullable();
            $table->string('updated_by');
            $table->string('pay_status')->default('Owe');
            $table->string('person_code')->nullable();
            $table->integer('person_id')->unsigned()->nullable();
            $table->foreign('person_id')->references('id')->on('people');
            $table->timestamp('order_date')->nullable();
            $table->string('driver')->nullable();
            $table->string('paid_by');
            $table->text('del_address')->nullable();
            $table->string('name');
            $table->string('po_no')->nullable();
            $table->decimal('total_qty', 12, 4)->nullable();
            $table->string('pay_method')->nullable();
            $table->string('note')->nullable();
            $table->datetime('paid_at')->nullable();
            $table->string('cancel_trace')->nullable();
            $table->string('contact')->nullable();
            $table->string('del_postcode')->nullable();
            $table->decimal('delivery_fee', 10, 2)->nullable();
            $table->text('bill_address')->nullable();
            $table->integer('digital_clock')->nullable();
            $table->integer('analog_clock')->nullable();
            $table->decimal('balance_coin', 10, 2)->nullable();
            $table->boolean('is_freeze')->default(0);
            $table->boolean('is_required_analog')->default(1);
            $table->integer('franchisee_id')->unsigned()->nullable();
            $table->integer('transaction_id')->unsigned();

            $table->timestamps();
            $table->softDeletes();

            //record who paid and updated

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ftransactions');
    }
}
