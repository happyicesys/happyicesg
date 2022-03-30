<?php

use App\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Country::create([
            'name' => 'Malaysia',
            'nationality_name' => 'Malaysian',
            'currency_name' => 'MYR',
            'currency_symbol' => 'RM',
            'phone_code' => '60',
            'is_city' => true,
            'is_state' => true,
        ]);

        Country::create([
            'name' => 'Singapore',
            'nationality_name' => 'Singaporean',
            'currency_name' => 'SGD',
            'currency_symbol' => 'S$',
            'phone_code' => '65',
            'is_city' => false,
            'is_state' => false,
        ]);

        Country::create([
            'name' => 'China',
            'nationality_name' => 'China',
            'currency_name' => 'RMB',
            'currency_symbol' => '¥',
            'phone_code' => '86',
            'is_city' => false,
            'is_state' => false,
        ]);
    }
}
