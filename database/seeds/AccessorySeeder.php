<?php

use Illuminate\Database\Seeder;
use App\Accessory;

class AccessorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Accessory::create([
            'name' => 'Light Box',
        ]);

        Accessory::create([
            'name' => 'Bell',
        ]); 

        Accessory::create([
            'name' => 'Menu Box',
        ]);

        Accessory::create([
            'name' => 'Key and Lock',
        ]);  

        Accessory::create([
            'name' => 'Pull Up Banner',
        ]); 

        Accessory::create([
            'name' => 'LED Menu Set',
        ]);                                    
    }
}
