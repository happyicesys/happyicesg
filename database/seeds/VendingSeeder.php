<?php

use Illuminate\Database\Seeder;
use App\Vending;

class VendingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	Vending::create([
    		'name' => 'Fun Vending Machine'
    	]);

    	Vending::create([
    		'name' => 'Honest Vending Machine'
    	]);

    	Vending::create([
    		'name' => 'Direct Vending Machine'
    	]);
    }
}
