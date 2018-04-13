<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $fillable = [
        'task_name', 'progress', 'remarks', 'task_date', 'created_by',
        'is_verify', 'updated_by'
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
}
