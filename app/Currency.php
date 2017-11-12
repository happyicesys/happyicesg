<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
    	'name', 'symbol'
    ];

    // getter and setter
    public function profiles()
    {
        return $this->belongsTo('App\Profile');
    }
}
