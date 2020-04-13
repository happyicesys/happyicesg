<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DriverLocation extends Model
{
    const STATUS_PENDING = 1;
    const STATUS_SUBMITTED = 2;
    const STATUS_APPROVED = 3;
    const STATUS_REJECTED = 99;

    protected $fillable = [
        'delivery_date', 'location_count', 'user_id', 'status', 'updated_by',
        'submission_date', 'daily_limit', 'remarks', 'approved_by', 'approved_at',
        'online_location_count'
    ];

    // relationships
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getDeliveryDateAttribute($value)
    {
        return Carbon::parse($value)->toDateString();
    }

    public function getSubmissionDateAttribute($value)
    {
        if($value) {
            return Carbon::parse($value)->toDateTimeString();
        }
    }

    public function getApprovedAtAttribute($value)
    {
        if($value) {
            return Carbon::parse($value)->toDateTimeString();
        }
    }
}
