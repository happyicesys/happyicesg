<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bomvending extends Model
{
    protected $fillable = [
        'custcategory_id', 'bomcomponent_id', 'bompart_id', 'person_id', 'updated_by'
    ];

    public function custcategory()
    {
    	return $this->belongsTo('App\Custcategory');
    }

    public function bompart()
    {
    	return $this->belongsTo('App\Bompart');
    }

    public function person()
    {
    	return $this->belongsTo('App\Person');
    }

    public function updater()
    {
        return $this->belongsTo('App\User', 'updated_by');
    }
}
