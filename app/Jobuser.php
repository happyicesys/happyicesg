<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Jobuser extends Model
{
    protected $fillable = [
        'user_id', 'job_id'
    ];

    // relationships
    public function job()
    {
        return $this->belongsTo('App\Job');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
