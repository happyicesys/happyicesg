<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Paysummaryinfo extends Model
{
    protected $fillable = [
    	'paid_at', 'pay_method', 'remark',
    	'profile_id', 'user_id'
    ];

    protected $dates = [
        'paid_at'
    ];

    // relationships
    public function profile()
    {
    	return $this->belongsTo('App\Profile');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
