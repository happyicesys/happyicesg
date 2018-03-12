<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bompart extends Model
{
    protected $fillable = [
    	'part_id', 'name', 'remark', 'bomcomponent_id', 'thumbnail_url',
        'updated_by', 'qty', 'movable', 'color', 'drawing_id', 'drawing_path',
        'supplier_order', 'unit_price', 'pic', 'bomgroup_id', 'price_remark'
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

    public function bompartconsumables()
    {
        return $this->hasMany('App\Bompartconsumable');
    }

    public function bomgroup()
    {
        return $this->belongsTo('App\Bomgroup');
    }

    public function updater()
    {
        return $this->belongsTo('App\User', 'updated_by');
    }

    public function drawings()
    {
        return $this->hasMany('App\Drawing');
    }
}
