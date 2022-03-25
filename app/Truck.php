<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Truck extends Model
{
    protected $fillable = [
        'name', 'desc', 'height', 'iu_number', 'user_id',
    ];

    public function driver()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
