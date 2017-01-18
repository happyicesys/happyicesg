<?php

use Illuminate\Database\Seeder;
use App\Month;

class MonthSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

    	foreach($months as $month){
	        Month::create([
	        	'name' => $month
	        ]);
    	}
    }
}
