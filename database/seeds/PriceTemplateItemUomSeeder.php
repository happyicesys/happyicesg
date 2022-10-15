<?php

use Illuminate\Database\Seeder;
use App\ItemUom;
use App\PriceTemplateItemUom;
use App\PriceTemplateItem;

class PriceTemplateItemUomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $priceTemplateItems = PriceTemplateItem::whereHas('item', function($query) {
            $query->where('is_inventory', true);
        })->get();

        $itemUoms = ItemUom::all();

        if($priceTemplateItems and $itemUoms) {
            foreach($priceTemplateItems as $priceTemplateItem) {
                foreach($itemUoms as $itemUom) {
                    if($priceTemplateItem->item_id == $itemUom->item_id) {
                        if($itemUom->uom->name == 'pcs') {
                            PriceTemplateItemUom::create([
                                'price_template_item_id' => $priceTemplateItem->id,
                                'item_uom_id' => $itemUom->id,
                            ]);
                        }
                    }
                }
            }
        }
    }
}
