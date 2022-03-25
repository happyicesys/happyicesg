<?php

use App\Truck;
use Illuminate\Database\Seeder;

class TruckSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Truck::create([
            'name' => 'GBE826J',
            'desc' => 'Tall Truck with Tail gate',
            'height' => 2.9,
            'user_id' => 100131,
        ]);

        Truck::create([
            'name' => 'GBE2306E',
            'desc' => 'Tall Cold Truck',
            'height' => 2.7,
            'user_id' => 100103,
        ]);

        Truck::create([
            'name' => 'GBF6627Y',
            'desc' => 'Tall Cold Truck',
            'height' => 2.7,
            'user_id' => 100145,
        ]);

        Truck::create([
            'name' => 'GBK7009Z',
            'desc' => 'Tall Cold Truck',
            'height' => 2.7,
            'user_id' => 100150,
        ]);

        Truck::create([
            'name' => 'GBG7001R',
            'desc' => 'Short Cold Truck',
            'height' => 2.0,
            'user_id' => 100132,
        ]);

        Truck::create([
            'name' => 'GBD4319K',
            'desc' => 'Tall Cold Truck with Tail gate',
            'height' => 2.7,
            'user_id' => 100154,
        ]);
    }
}
