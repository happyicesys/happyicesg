<?php

use Illuminate\Database\Seeder;
use App\Month;

class ShortMonthSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec'];

        for($i=1; $i<=12; $i++) {
            $month = Month::findOrFail($i);
            $month->short_name = $months[$i - 1];
            $month->save();
        }
    }
}
