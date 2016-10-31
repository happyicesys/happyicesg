<?php

use Illuminate\Database\Seeder;
use App\D2dOnlineSale;

class D2dOnlineSaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        D2dOnlineSale::create([
            'sequence' => 1,
            'caption' => 'Red Bean Jelly (5pcs/ box)',
            'item_id' => 9,
            'qty_divisor' => 6,
            'person_id' => 1643
        ]);

        D2dOnlineSale::create([
            'sequence' => 2,
            'caption' => 'Chocolate Pie with Mango (5pcs/ box)',
            'item_id' => 11,
            'qty_divisor' => 6,
            'person_id' => 1643
        ]);

        D2dOnlineSale::create([
            'sequence' => 3,
            'caption' => 'QQ Pudding (5pcs/ box)',
            'item_id' => 10,
            'qty_divisor' => 6,
            'person_id' => 1643
        ]);

        D2dOnlineSale::create([
            'sequence' => 4,
            'caption' => 'Green Mango & Lime (5pcs/ box)',
            'item_id' => 6,
            'qty_divisor' => 6,
            'person_id' => 1643
        ]);

        D2dOnlineSale::create([
            'sequence' => 5,
            'caption' => 'Chocolate Roll (5pcs/ flavor)',
            'item_id' => 4,
            'qty_divisor' => 3,
            'person_id' => 1643
        ]);

        D2dOnlineSale::create([
            'sequence' => 6,
            'caption' => 'Vanilla Roll (5pcs/ flavor)',
            'item_id' => 5,
            'qty_divisor' => 3,
            'person_id' => 1643
        ]);

        D2dOnlineSale::create([
            'sequence' => 7,
            'caption' => 'Matcha Roll (5pcs/ flavor)',
            'item_id' => 26,
            'qty_divisor' => 3,
            'person_id' => 1643
        ]);

        D2dOnlineSale::create([
            'sequence' => 8,
            'caption' => 'Strawberry (6pcs/ set)',
            'item_id' => 18,
            'qty_divisor' => 5,
            'person_id' => 1643
        ]);

        D2dOnlineSale::create([
            'sequence' => 9,
            'caption' => 'Mint Chocolate (6pcs/ set)',
            'item_id' => 19,
            'qty_divisor' => 6,
            'person_id' => 1643
        ]);
    }
}
