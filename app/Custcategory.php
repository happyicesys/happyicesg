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
    	return $this->hasMany('App\Person');
    }

    public function bomtemplate()
    {
    	return $this->hasMany('App\Bomtemplate');
    }
}
