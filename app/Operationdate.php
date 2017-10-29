<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Operationdate extends Model
{
    protected $fillable = [
    	'delivery_date', 'color',
    	'person_id'
    ];

    // relationships
    public function person()
    {
    	return $this->belongsTo('App\Person');
    }

    // getter and setter
    public function getDeliveryDateAttribute($date)
    {
        return $date ? Carbon::parse($date)->format('Y-m-d') : null;
    }
}

