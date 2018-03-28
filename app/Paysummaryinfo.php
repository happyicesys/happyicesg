<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Paysummaryinfo extends Model
{
    protected $fillable = [
    	'paid_at', 'pay_method', 'remark', 'bankin_date',
    	'profile_id', 'user_id', 'is_verified'
    ];

    protected $dates = [
        'paid_at', 'bankin_date'
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

    // getter and setter
    public function getBankinDateAttribute($date)
    {
        return $date ? Carbon::parse($date)->format('Y-m-d') : null;
    }
}
