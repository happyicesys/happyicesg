<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PriceTemplateItem extends Model
{
    protected $fillable = [
        'sequence',
        'item_id',
        'price_template_id',
        'retail_price',
        'quote_price',
    ];

    // relationships
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function priceTemplate()
    {
        return $this->belongsTo(PriceTemplate::class);
    }

    public function priceTemplateItemUoms()
    {
        return $this->hasMany(PriceTemplateItemUom::class);
    }
}
