<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InvRecord extends Model
{
    protected $table = 'invrecords';

    protected $fillable = [

        'inventory_id', 'qtyrec_current', 'qtyrec_incoming',
        'qtyrec_after'

    ];

    public function inventory()
    {
        return $this->belongsTo('App\Inventory');
    }

    public function item()
    {
        return $this->belongsTo('App\Item');
    }
}
