<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bompartconsumable extends Model
{
    protected $fillable = [
    	'partconsumable_id', 'name', 'remark', 'bompart_id',
        'updated_by', 'qty', 'color', 'supplier_order', 'unit_price',
        'pic', 'drawing_id', 'drawing_path', 'bomgroup_id'
    ];


    public function bompartconsumablecustcat()
    {
        return $this->hasMany('App\Bompartconsumablecustcat');
    }

    public function bompart()
    {
    	return $this->belongsTo('App\Bompart');
    }

    public function drawings()
    {
        return $this->hasMany('App\Drawing');
    }

    public function bomgroup()
    {
    	return $this->belongsTo('App\Bomgroup');
    }

    public function updater()
    {
        return $this->belongsTo('App\User', 'updated_by');
    }
}
