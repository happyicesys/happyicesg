<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Truck extends Model
{
    protected $fillable = [
        'name', 'desc', 'height', 'iu_number',
    ];

    public function driver()
    {
        return $this->hasOne(User::class, 'truck_id');
    }
}
