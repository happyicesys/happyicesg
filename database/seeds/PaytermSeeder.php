<?php

use Illuminate\Database\Seeder;
use App\Payterm;

class PaytermSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Payterm::create([
            'name' => 'C.O.D',
        ]);

        Payterm::create([
            'name' => 'Prepaid',
        ]);

        Payterm::create([
            'name' => 'In a Given # of Days',
        ]);

        Payterm::create([
            'name' => 'On a Day of the Month',
        ]);

        Payterm::create([
            'name' => '# of Days after EOM',
        ]);

        Payterm::create([
            'name' => 'Day of Month after EOM',
        ]);                                        
    }
}
