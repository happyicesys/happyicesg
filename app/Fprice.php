<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fprice extends Model
{
    protected $table = 'fprices';

    protected $fillable = [
        'retail_price', 'quote_price', 'remark',
		'item_id', 'person_id'
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
