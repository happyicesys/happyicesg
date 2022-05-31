<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ItemUom extends Model
{
    protected $fillable = [
        'item_id',
        'uom_id',
        'is_base_unit',
        'is_transacted_unit',
        'value',
    ];

    // relationships
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function uom()
    {
        return $this->belongsTo(Uom::class);
    }
}
