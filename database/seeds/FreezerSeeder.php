<?php

use Illuminate\Database\Seeder;
use App\Freezer;

class FreezerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Freezer::create([
            'name' => 'Hiron SD-151',
        ]);

        Freezer::create([
            'name' => 'Hiron SD-280',
        ]);

        Freezer::create([
            'name' => 'Liebherr 0.8M',
        ]);

        Freezer::create([
            'name' => 'Liebherr 1M',
        ]);

        Freezer::create([
            'name' => 'Liebherr 1.2M',
        ]);        

        Freezer::create([
            'name' => 'Jackies 1M',
        ]);                                 
    }
}
