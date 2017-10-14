<?php

use Illuminate\Database\Seeder;
use App\Currency;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Currency::create([
            'name' => 'Singapore Dollar',
            'symbol' => 'S$'
        ]);

        Currency::create([
            'name' => 'Malaysia Ringgit',
            'symbol' => 'MYR'
        ]);
    }
}
