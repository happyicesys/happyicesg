<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bompart extends Model
{
    protected $fillable = [
    	'part_id', 'name', 'remark', 'bomcomponent_id'
    ];

    // relationships
    public function bompartfiles()
    {
    	return $this->hasMany('App\Bompartfiles');
    }

    public function bomcomponent()
    {
    	return $this->belongsTo('App\Bomcomponent');
    }
}
