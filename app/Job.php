<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Job extends Model
{
    protected $fillable = [
        'task_name', 'progress', 'remarks', 'task_date', 'created_by',
        'is_verify', 'updated_by'
    ];

    protected $dates = [
        'task_date'
    ];

    // relationships
    public function workers()
    {
        return $this->hasMany('App\Jobuser');
    }

    public function creator()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo('App\User', 'updated_by');
    }    

    // getter setter
    public function getTaskDateAttribute($date)
    {
        if ($date) {
            return Carbon::parse($date)->format('Y-m-d');
        } else {
            return null;
        }
    }    
}
