<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bomcategory extends Model
{
    protected $fillable = [
    	'category_id', 'name', 'remark', 'updated_by', 'drawing_id',
        'drawing_path'
    ];

    // relationships
    public function bomcomponents()
    {
    	return $this->hasMany('App\Bomcomponent');
    }

    public function bomcategorycustcat()
    {
        return $this->hasMany('App\Bomcategorycustcat');
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
