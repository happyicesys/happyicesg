<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Chrisbjr\ApiGuard\Models\Mixins\Apikeyable;

class Vending extends Model
{
    // use Apikeyable;
    
    protected $fillable = [
        'vend_id', 'serial_no', 'type', 'router', 'desc',
        'person_id', 'updated_by'
    ];

    // relationships
    public function person()
    {
    	return $this->belongsTo('App\Person');
    }

    public function vmhistories()
    {
        return $this->hasMany('App\Vmhistory');
    }

    public function updater()
    {
        return $this->belongsTo('App\User', 'updated_by');
    }    
}
