<?php

use Illuminate\Database\Seeder;
use App\Zone;

class ZoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Zone::create([
            'name' => 'North'
        ]);

        Zone::create([
            'name' => 'West'
        ]);

        Zone::create([
            'name' => 'East'
        ]);

        Zone::create([
            'name' => 'Others'
        ]);
    }
}
