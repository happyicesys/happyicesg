<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CalendarEvent extends Model
{
    protected $fillable = [
        'title',
        'desc',
        'start_date',
        'end_date',
        'background_color',
        'color',
        'is_full_day',
        'working_shift_item_id',
    ];
}
