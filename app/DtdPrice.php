<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DtdPrice extends Model
{
    protected $table = 'dtdprices';

    protected $fillable = [
        'retail_price', 'quote_price', 'remark',
        'created_by', 'updated_by', 'item_id'
    ];

    public function item()
    {
        return $this->belongsTo('App\Item');
    }
}
