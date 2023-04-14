<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexesDealsTransactionsProfilesItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deals', function (Blueprint $table) {
            $table->index('item_id')->change();
            $table->index('transaction_id')->change();
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->index('person_id')->change();
            $table->index('delivery_date')->change();
            $table->index('is_required_analog')->change();
        });

        Schema::table('people', function (Blueprint $table) {
            $table->index('profile_id')->change();
        });

        Schema::table('items', function (Blueprint $table) {
            $table->index('product_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('deals', function (Blueprint $table) {
            //
        });
    }
}
