<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Personmaintenance extends Model
{
    protected $fillable = [
        'person_id', 'title', 'remarks', 'updated_by', 'is_refund',
        'refund_name', 'refund_bank', 'refund_contact', 'created_by', 'refund_account',
        'created_at', 'complete_date', 'is_verify'
    ];

    // relationship
    public function person()
    {
        return $this->belongsTo('App\Person');
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
    public function setCompleteDateAttribute($value)
    {
        $this->attributes['complete_date'] = $value ? : null;
    }

    public function getCompleteDateAttribute($date)
    {
        if ($date) {
            return Carbon::parse($date)->format('Y-m-d');
        } else {
            return null;
        }
    }    
}
