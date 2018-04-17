<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Overtime extends Model
{
    protected $fillable = [
        'user_id', 'created_by', 'updated_by', 'overtime_date', 'hours',
        'remarks'
    ];

    // relationship
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function updater()
    {
        return $this->belongsTo('App\User', 'updated_by');
    }

    public function creator()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    // getter setter
    public function setOvertimeDateAttribute($value)
    {
        $this->attributes['overtime_date'] = $value ? : null;
    }

    public function getOvertimeDateAttribute($date)
    {
        if ($date) {
            return Carbon::parse($date)->format('Y-m-d');
        } else {
            return null;
        }
    }
}
