<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bompartfile extends Model
{
    protected $fillable = [
        'path', 'name', 'part_id'
    ];

    public function getCreatedAtAttribute($date)
    {
        return Carbon::parse($date)->format('d-M-Y');
    }

    public function part()
    {
        return $this->belongsTo('App\Part');
    }
}
