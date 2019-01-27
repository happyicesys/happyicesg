<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transactionpersonasset extends Model
{
    protected $fillable = [
        'personasset_id', 'transaction_id', 'serial_no', 'sticker', 'qty',
        'is_warehouse', 'datein', 'dateout', 'remarks'
    ];

    public function transaction()
    {
        return $this->belongsTo('App\Transaction');
    }

    public function personasset()
    {
        return $this->belongsTo('App\Personasset');
    }
}
