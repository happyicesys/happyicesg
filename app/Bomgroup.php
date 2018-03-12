<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bomgroup extends Model
{
    protected $fillable = [
    	'name', 'prefix', 'remark', 'updated_by'
    ];

    public function bomparts()
    {
    	return $this->hasMany('App\Bompart');
    }

    public function bompartconsumables()
    {
    	return $this->hasMany('App\Bompartconsumable');
    }

    public function bomcomponents()
    {
        return $this->hasMany('App\Bomcomponent');
    }

    public function updater()
    {
        return $this->belongsTo('App\User', 'updated_by');
    }
}
