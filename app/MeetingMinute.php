<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class MeetingMinute extends Model
{
    protected $fillable = [
        'date', 'details', 'created_by', 'updated_by'
    ];

    // relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // getter
    public function getDateAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d (ddd)');
    }
}
