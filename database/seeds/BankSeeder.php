<?php

use Illuminate\Database\Seeder;
use App\Bank;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Bank::create([
            'name' => 'DBS'
        ]);

        Bank::create([
            'name' => 'OCBC'
        ]);

        Bank::create([
            'name' => 'UOB'
        ]);

        Bank::create([
            'name' => 'Maybank'
        ]);

        Bank::create([
            'name' => 'HSBC'
        ]);

        Bank::create([
            'name' => 'Standard Chartered Bank'
        ]);

        Bank::create([
            'name' => 'Bank of China'
        ]);

        Bank::create([
            'name' => 'CIMB'
        ]);
    }
}
