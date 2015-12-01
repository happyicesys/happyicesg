<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Deal extends Model
{
    protected $fillable = [
        'item_id', 'transaction_id', 'qty',
        'amount'
    ];

    public function item()
    {
        return $this->belongsTo('App\Item');
    }

    public function transaction()
    {
        return $this->belongsTo('App\Transaction');
    }
}
