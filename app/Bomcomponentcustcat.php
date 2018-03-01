<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bomcomponentcustcat extends Model
{
    protected $fillable = [
        'custcategory_id', 'bomcomponent_id', 'updated_by'
    ];

    public function custcategory()
    {
    	return $this->belongsTo('App\Custcategory');
    }

    public function bomcomponent()
    {
    	return $this->belongsTo('App\Bomcomponent');
    }

    public function updater()
    {
        return $this->belongsTo('App\User', 'updated_by');
    }
}
