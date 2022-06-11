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
        ]);

        Uom::create([
            'name' => 'box',
            'sequence' => 2,
        ]);

        Uom::create([
            'name' => 'ctn',
            'sequence' => 3,
        ]);
    }
}
