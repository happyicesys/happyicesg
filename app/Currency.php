<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
    	'name', 'symbol', 'rate', 'updated_by'
    ];

    // getter and setter
    public function profiles()
    {
        return $this->belongsTo('App\Profile');
    }

    public function updater()
    {
        return $this->belongsTo('App\User', 'updated_by');
    }
}
