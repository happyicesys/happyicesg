<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'name',
        'date_from',
        'date_to',
        'desc',
        'status',
        'status_json',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'status_json' => 'array',
    ];

    const STATUS_PENDING = 1;
    const STATUS_COMPLETED = 2;
    const STATUS_CANCELLED = 99;

    const STATUSES = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_COMPLETED => 'Completed',
        self::STATUS_CANCELLED => 'Cancelled',
    ];

    // relationships
    public function createdBy()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo('App\User', 'updated_by');
    }
}
