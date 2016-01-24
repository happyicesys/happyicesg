<?php

use Illuminate\Database\Seeder;
use App\Item;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Item::create([
            'product_id' => '001',
            'name' => 'CHOCOLATE CUP',
            'desc' => '(10 cups/bag)',
            'unit' => 'BAG',
        ]);

        Item::create([
            'product_id' => '002',
            'name' => 'MELON ICE CREAM',
            'desc' => '(10 cups/bag)',
            'unit' => 'BAG',
        ]);

        Item::create([
            'product_id' => '003',
            'name' => 'VANILLA ICE CREAM',
            'desc' => '(10 cups/bag)',
            'unit' => 'BAG',
        ]);

        Item::create([
            'product_id' => '004',
            'name' => 'DASHENG ROLL CHOCOLATE',
            'desc' => '(15 pcs/ctn)',
            'unit' => 'CTN',
        ]);

        Item::create([
            'product_id' => '005',
            'name' => 'DASHENG ROLL VANILLA',
            'desc' => '(15 pcs/ctn)',
            'unit' => 'CTN',
        ]);

        //another batch

        Item::create([
            'product_id' => '010',
            'name' => 'GREEN MANGO & LEMON',
            'desc' => '(30 pcs/6 boxes per ctn)',
            'unit' => 'CTN',
        ]);

        Item::create([
            'product_id' => '011',
            'name' => 'RED BEAN TARO ICE CREAM BAR',
            'desc' => '(30 pcs/6 boxes per ctn)',
            'unit' => 'CTN',
        ]); 

        Item::create([
            'product_id' => '012',
            'name' => 'TARO CHUNKS ICE CREAM BAR',
            'desc' => '(30 pcs/6 boxes per ctn)',
            'unit' => 'CTN',
        ]); 

        Item::create([
            'product_id' => '013',
            'name' => 'RED BEAN JELLY ICE CREAM BAR',
            'desc' => '(30 pcs/6 boxes per ctn)',
            'unit' => 'CTN',
        ]); 
        
        Item::create([
            'product_id' => '014',
            'name' => 'QQ PUDDING ICE CREAM BAR',
            'desc' => '(30 pcs/6 boxes per ctn)',
            'unit' => 'CTN',
        ]);

        Item::create([
            'product_id' => '015',
            'name' => 'CHOC PIE W/ MANGO ICE CREAM',
            'desc' => '(30 pcs/6 boxes per ctn)',
            'unit' => 'CTN',
        ]); 

        Item::create([
            'product_id' => '016',
            'name' => 'CHOC PIE W/ PEANUT ICE CREAM',
            'desc' => '(30 pcs/6 boxes per ctn)',
            'unit' => 'CTN',
        ]); 

        Item::create([
            'product_id' => '017',
            'name' => 'CHOC PIE W/ VANILLA ICE CREAM',
            'desc' => '(30 pcs/6 boxes per ctn)',
            'unit' => 'CTN',
        ]);                                                                                                   

        //ANOTHER
        Item::create([
            'product_id' => '019',
            'name' => 'QQ -JAPANESE MATCHA',
            'desc' => '(24 pcs/6 boxes per ctn)',
            'unit' => 'CTN',
        ]);

        Item::create([
            'product_id' => '020',
            'name' => 'PINEAPPLE & GUAVA ICE CREAM BAR',
            'desc' => '(30 pcs/6 boxes per ctn)',
            'unit' => 'CTN',
        ]);

        Item::create([
            'product_id' => '021',
            'name' => 'LOW FAT FROZEN YOGURT - VANILLA',
            'desc' => '(30cups/ctn)',
            'unit' => 'CTN',
        ]);

        Item::create([
            'product_id' => '022',
            'name' => 'LOW FAT FROZEN YOGURT - MANGO',
            'desc' => '(30cups/ctn)',
            'unit' => 'CTN',
        ]);

        Item::create([
            'product_id' => '023',
            'name' => 'LOW FAT FROZEN YOGURT - STRAWBERRY',
            'desc' => '(30cups/ctn)',
            'unit' => 'CTN',
        ]);               

        Item::create([
            'product_id' => '026',
            'name' => 'OSHARE - MINT CHOCOLATE',
            'desc' => '(6cups/bag)',
            'unit' => 'CTN',
        ]);

    }
}
