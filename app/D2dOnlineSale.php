<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class D2dOnlineSale extends Model
{
    protected $table = 'd2d_online_sales';

    protected $fillable = [
        'caption', 'sequence', 'qty_divisor',
        'item_id', 'person_id', 'coverage'
    ];

    // getter
    public function getQtyDivisorAttribute($value)
    {
        return $value + 0;
    }

    // relationships
    public function item()
    {
        return $this->belongsTo('App\Item');
    }

    public function person()
    {
        return $this->belongsTo('App\Person');
    }
}
