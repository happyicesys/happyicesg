<?php

use Illuminate\Database\Seeder;
use App\Item;
use App\ItemUom;
use App\Uom;

class ItemUomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = Item::where('is_inventory', 1)->get();

        foreach($items as $item) {
            if($item->base_unit) {
                $pcsUom = Uom::where('name', 'pcs')->first();
                ItemUom::create([
                    'item_id' => $item->id,
                    'uom_id' => $pcsUom->id,
                    'is_base_unit' => true,
                    'value' => 1,
                ]);

                $ctnUom = Uom::where('name', 'ctn')->first();
                ItemUom::create([
                    'item_id' => $item->id,
                    'uom_id' => $ctnUom->id,
                    'is_transacted_unit' => true,
                    'value' => $item->base_unit,
                ]);
            }
        }
    }
}
