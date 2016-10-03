<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class D2dOnlineSale extends Model
{
    protected $table = 'd2d_online_sales';

    protected $fillable = [
        'unit_price', 'qty_divisor', 'desc',

        'item_id'
    ];

    // relationships
    public function item()
    {
        return $this->belongsTo('App\Item');
    }
}
