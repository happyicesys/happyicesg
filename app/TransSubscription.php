<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransSubscription extends Model
{
    protected $fillable = [
    	'user_id'
    ];

    // getter and setter
    public function user()
    {
    	return $this->belongsTo('App\User');
    }
}
