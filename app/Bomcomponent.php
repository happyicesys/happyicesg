<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bomcomponent extends Model
{
    protected $fillable = [
        'component_id', 'name', 'remark', 'updated_by', 'bomcategory_id'
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

    public function updater()
    {
        return $this->belongsTo('App\User', 'updated_by');
    }
}
