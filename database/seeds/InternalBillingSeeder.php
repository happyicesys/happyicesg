<?php

use Illuminate\Database\Seeder;
use App\Profile;
use App\Payterm;

class InternalBillingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$payterm = Payterm::whereName('15 Days after EOM')->first();
    	$hil = Profile::whereName('HAPPY ICE LOGISTIC PTE LTD')->first();
    	$hi = Profile::whereName('HAPPY ICE PTE LTD')->first();
    	$id = Profile::whereName('ICE DROP PTE LTD')->first();

    	$hil->acronym = 'HIL';
        $hil->payterm_id = $payterm->id;
    	$hil->save();
    	$hi->acronym = 'HI';
        $hi->payterm_id = $payterm->id;
    	$hi->save();
    	$id->acronym = 'ID';
        $id->payterm_id = $payterm->id;
    	$id->save();
    }
}
