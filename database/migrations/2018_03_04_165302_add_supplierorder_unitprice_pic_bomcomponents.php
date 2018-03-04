<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSupplierorderUnitpricePicBomcomponents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bomcomponents', function($table) {
            $table->string('supplier_order')->nullable();
            $table->decimal('unit_price', 10, 2);
            $table->string('pic')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bomcomponents', function($table) {
            $table->dropColumn('supplier_order');
            $table->dropColumn('unit_price');
            $table->dropColumn('pic');
        });
    }
}
