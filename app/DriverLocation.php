<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DriverLocation extends Model
{
    protected $fillable = [
        'delivery_date', 'location_count', 'user_id'
    ];
}
