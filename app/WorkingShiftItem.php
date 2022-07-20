<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkingShiftItem extends Model
{

    const TYPE = [
        1 => [
            'title' => 'Public Holiday',
            'label' => 'p_holiday',
            'is_full_day' => true
        ],
        2 => [
            'title' => 'Special Holiday',
            'label' => 's_holiday',
            'is_full_day' => true
        ],
        3 => [
            'title' => 'Off Day',
            'label' => 'off',
            'is_full_day' => true
        ],
        4 => [
            'title' => 'Annual Leave',
            'label' => 'AL',
            'is_full_day' => true
        ],
        5 => [
            'title' => 'Unpaid Leave',
            'label' => 'UL',
            'is_full_day' => true
        ],
    ];

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
    public function holiday()
    {
        return $this->belongsTo(Holiday::class);
    }

    public function workingShift()
    {
        return $this->belongsTo(WorkingShift::class);
    }
}
