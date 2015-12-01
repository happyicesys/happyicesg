<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    protected $fillable = [
        'retail_price', 'quote_price', 'remark',
        'person_id', 'item_id'
    ];

    public function person()
    {
        return $this->belongsTo('App\Person');
    }

    public function item()
    {
        return $this->belongsTo('App\Item');
    }
}
