<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bompart extends Model
{
    protected $fillable = [
    	'part_id', 'name', 'remark', 'bomcomponent_id', 'thumbnail_url',
        'updated_by', 'qty', 'movable', 'color'
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

    public function bomtemplates()
    {
        return $this->hasMany('App\Bomtemplate');
    }

    public function bomvendings()
    {
        return $this->hasMany('App\Bomvending');
    }

    public function updater()
    {
        return $this->belongsTo('App\User', 'updated_by');
    }
}
