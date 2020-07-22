<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class OutletVisit extends Model
{
    const OUTCOMES = [
        1 => 'Meet PIC & pass order form',
        2 => 'Meet PIC & pass order form, say will place order',
        3 => 'No meet PIC, only pass the order form',
        4 => 'E-contact PIC, say will order',
        5 => 'E-contact PIC, no response',
        6 => 'E-contact PIC, say don\'t need stock',
        7 => 'Please refer remarks'
    ];

    protected $fillable = [
        'date', 'day', 'outcome', 'remarks', 'created_by', 'updated_by', 'person_id'
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

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    // getter setter
    public function getDateAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d');
    }
}
