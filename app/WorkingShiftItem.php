<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkingShiftItem extends Model
{
    protected $fillable = [
        'is_every_week',
        'day_number',
        'type',
        'label',
        'working_shift_id',
        'start_date',
        'end_date',
        'holiday_id'
    ];

    // relationships
    public function workingShift()
    {
        return $this->belongsTo(WorkingShift::class);
    }
}
