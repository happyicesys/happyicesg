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

    public function potentialCustomers()
    {
        return $this->hasMany(Person::class)->where('active', 'Potential');
    }

    public function confirmedCustomers()
    {
        return $this->hasMany(Person::class)->where('active', 'Yes');
    }
}
