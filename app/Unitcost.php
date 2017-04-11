<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Unitcost extends Model
{
    protected $fillable = [
    	'unit_cost',
    	'item_id', 'profile_id'
    ];

    public function item()
    {
        return $this->belongsTo('App\Item');
    }

    public function profile()
    {
        return $this->belongsTo('App\Profile');
    }

    // getter and setter
    public function setUnitCostAttribute($value)
    {
        $this->attributes['unit_cost'] = $value ?: null;
    }
}
