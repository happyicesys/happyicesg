<?php

use Illuminate\Database\Seeder;
use App\Itemcategory;

class ItemcategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Itemcategory::create([
        	'name' => 'Roll'
        ]);

        Itemcategory::create([
        	'name' => 'Stick'
        ]);

        Itemcategory::create([
        	'name' => 'Frozen Yogurt'
        ]);

        Itemcategory::create([
        	'name' => 'Cup'
        ]);
    }
}
