<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'year'
    ];

    // relationships
    public function workingShiftItems()
    {
        return $this->hasMany(WorkingShiftItem::class);
    }

}
