<?php

use Illuminate\Database\Seeder;
use App\Unit;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Unit::create([
            'name' => 'UNIT',
        ]);

        Unit::create([
            'name' => 'BAG',
        ]); 

        Unit::create([
            'name' => 'CTN',
        ]);

        Unit::create([
            'name' => 'BOX',
        ]); 

        Unit::create([
            'name' => 'CUP',
        ]);

        Unit::create([
            'name' => 'PCS',
        ]);                                            
    }
}
