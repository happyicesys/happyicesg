<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OnlinePrice extends Model
{
    protected $table = 'onlinePrices';

    protected $fillable = [
        'retail_price', 'divident', 'divisor',
        'remark', 'created_by', 'updated_by',
    ];

    public function item()
    {
        return $this->belongsTo('App\Item');
    }
}
