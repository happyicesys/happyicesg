<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bomcomponent extends Model
{
    protected $fillable = [
        'component_id', 'name', 'remark', 'updated_by', 'bomcategory_id',
        'drawing_id', 'drawing_path', 'supplier_order', 'unit_price', 'pic',
        'bomgroup_id'
    ];

    // relationships
    public function bomcategory()
    {
    	return $this->belongsTo('App\Bomcategory');
    }

    public function bomparts()
    {
    	return $this->hasMany('App\Bompart');
    }

    public function bomcomponentcustcat()
    {
        return $this->hasMany('App\Bomcomponentcustcat');
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
