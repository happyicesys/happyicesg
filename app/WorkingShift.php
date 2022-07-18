<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkingShift extends Model
{
    protected $fillable = [
        'worker_id',
    ];

    public function worker()
    {
        return $this->belongsTo(User::class, 'worker_id');
    }

    public function workingShiftItems()
    {
        return $this->hasMany(WorkingShiftItem::class);
    }
}
