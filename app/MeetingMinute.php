<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MeetingMinutes extends Model
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
}
