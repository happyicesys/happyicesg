<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Custcategory extends Model
{
    protected $fillable = [
    	'name', 'desc'
    ];

    // relationships
    public function people()
    {
    	return $this->belongsToMany('App\Person');
    }

    public function bomtemplates()
    {
    	return $this->hasMany('App\Bomtemplate');
    }

    public function bomvendings()
    {
        return $this->hasMany('App\Bomvending');
    }
}
