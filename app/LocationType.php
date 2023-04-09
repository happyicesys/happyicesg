<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LocationType extends Model
{
    protected $fillable = [
        'sequence',
        'name',
        'remarks',
    ];

    public function people()
    {
        return $this->hasMany(Person::class);
    }
}
