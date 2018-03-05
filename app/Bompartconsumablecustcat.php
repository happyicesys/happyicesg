<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bompartconsumablecustcat extends Model
{
    protected $fillable = [
        'custcategory_id', 'bompartconsumable_id', 'updated_by'
    ];

    public function custcategory()
    {
    	return $this->belongsTo('App\Custcategory');
    }

    public function bompartconsumable()
    {
    	return $this->belongsTo('App\Bompartconsumable');
    }

    public function updater()
    {
        return $this->belongsTo('App\User', 'updated_by');
    }
}
