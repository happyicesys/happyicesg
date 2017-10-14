<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vending extends Model
{
    protected $fillable = [
    	'name'
    ];

    // relationships
    public function people()
    {
    	return $this->belongsToMany('App\Person');
    }
}
