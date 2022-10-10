<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PriceTemplateItemUom extends Model
{
    protected $fillable = [
        'price_template_item_id',
        'item_uom_id',
        'is_active',
    ];

    // relationships
    public function priceTemplateItem()
    {
        return $this->belongsTo(PriceTemplateItem::class)->with('priceTemplate');
    }

    public function itemUom()
    {
        return $this->belongsTo(ItemUom::class)->with(['item', 'uom']);
    }
}
