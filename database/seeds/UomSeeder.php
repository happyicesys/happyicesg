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
            'name' => 'pcs'
        ]);

        Uom::create([
            'name' => 'box'
        ]);

        Uom::create([
            'name' => 'ctn'
        ]);
    }
}
