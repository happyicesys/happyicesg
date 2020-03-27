<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DriverLocation extends Model
{
    const STATUS_SUBMITTED = 1;
    const STATUS_APPROVED = 2;
    const STATUS_REJECTED = 99;

    protected $fillable = [
        'delivery_date', 'location_count', 'user_id', 'status', 'updated_by',
        'submission_date'
    ];

    // relationships
    public function updated_by()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
