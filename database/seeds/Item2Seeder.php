<?php

use Illuminate\Database\Seeder;
use App\Item;

class Item2Seeder extends Seeder
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
            'remark' => '(10 cups/ bag)',
            'unit' => 'BAG',
        ]);

        Item::create([
            'product_id' => '002',
            'name' => 'MELON ICE CREAM',
            'remark' => '(10 cups/ bag)',
            'unit' => 'BAG',
        ]);

        Item::create([
            'product_id' => '003',
            'name' => 'VANILLA ICE CREAM',
            'remark' => '(10 cups/ bag)',
            'unit' => 'BAG',
        ]);

        Item::create([
            'product_id' => '004',
            'name' => 'DASHENG ROLL CHOCOLATE',
            'remark' => '(15 pcs/ ctn)',
            'unit' => 'CTN',
        ]);

        Item::create([
            'product_id' => '005',
            'name' => 'DASHENG ROLL VANILLA',
            'remark' => '(15 pcs/ ctn)',
            'unit' => 'CTN',
        ]);

        //another batch

        Item::create([
            'product_id' => '010',
            'name' => 'GREEN MANGO & LEMON',
            'remark' => '(30 pcs x 6 boxes/ ctn)',
            'unit' => 'CTN',
        ]);

        Item::create([
            'product_id' => '011',
            'name' => 'RED BEAN TARO ICE CREAM BAR',
            'remark' => '(30 pcs x 6 boxes/ ctn)',
            'unit' => 'CTN',
        ]); 

        Item::create([
            'product_id' => '012',
            'name' => 'TARO CHUNKS ICE CREAM BAR',
            'remark' => '(30 pcs x 6 boxes/ ctn)',
            'unit' => 'CTN',
        ]); 

        Item::create([
            'product_id' => '013',
            'name' => 'RED BEAN JELLY ICE CREAM BAR',
            'remark' => '(30 pcs x 6 boxes/ ctn)',
            'unit' => 'CTN',
        ]); 
        
        Item::create([
            'product_id' => '014',
            'name' => 'QQ PUDDING ICE CREAM BAR',
            'remark' => '(30 pcs x 6 boxes/ ctn)',
            'unit' => 'CTN',
        ]);

        Item::create([
            'product_id' => '015',
            'name' => 'CHOC PIE W/ MANGO ICE CREAM',
            'remark' => '(30 pcs x 6 boxes/ ctn)',
            'unit' => 'CTN',
        ]); 

        Item::create([
            'product_id' => '016',
            'name' => 'CHOC PIE W/ PEANUT ICE CREAM',
            'remark' => '(30 pcs x 6 boxes/ ctn)',
            'unit' => 'CTN',
        ]); 

        Item::create([
            'product_id' => '017',
            'name' => 'CHOC PIE W/ VANILLA ICE CREAM',
            'remark' => '(30 pcs x 6 boxes/ ctn)',
            'unit' => 'CTN',
        ]);                                                                                                   

        //ANOTHER
        Item::create([
            'product_id' => '019',
            'name' => 'QQ -JAPANESE MATCHA',
            'remark' => '(24 pcs x 6 boxes/ ctn)',
            'unit' => 'CTN',
        ]);

        Item::create([
            'product_id' => '020',
            'name' => 'PINEAPPLE & GUAVA ICE CREAM BAR',
            'remark' => '(30 pcs x 6 boxes/ ctn)',
            'unit' => 'CTN',
        ]);

        Item::create([
            'product_id' => '021',
            'name' => 'LOW FAT FROZEN YOGURT - VANILLA',
            'remark' => '(30 cups/ ctn)',
            'unit' => 'CTN',
        ]);

        Item::create([
            'product_id' => '022',
            'name' => 'LOW FAT FROZEN YOGURT - MANGO',
            'remark' => '(30 cups/ ctn)',
            'unit' => 'CTN',
        ]);

        Item::create([
            'product_id' => '023',
            'name' => 'LOW FAT FROZEN YOGURT - STRAWBERRY',
            'remark' => '(30 cups/ ctn)',
            'unit' => 'CTN',
        ]);              

        Item::create([
            'product_id' => '026',
            'name' => 'OSHARE - MINT CHOCOLATE',
            'remark' => '(6 cups/ bag)',
            'unit' => 'CTN',
        ]);
    }
}
