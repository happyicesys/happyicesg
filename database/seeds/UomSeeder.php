<?php

use Illuminate\Database\Seeder;
use App\Uom;

class UomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Uom::create([
            'name' => 'pcs',
            'sequence' => 1,
            'color' => '#f0f0f0'
        ]);

        Uom::create([
            'name' => 'box',
            'sequence' => 2,
            'color' => '#dddddd'
        ]);

        Uom::create([
            'name' => 'ctn',
            'sequence' => 3,
            'color' => '#c9c9c9'
        ]);
    }
}
