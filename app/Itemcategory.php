<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Itemcategory extends Model
{
    protected $fillable = [
    	'name'
    ];

    // relationships
    public function items()
    {
    	return $this->hasMany('App\Item');
    }
}
