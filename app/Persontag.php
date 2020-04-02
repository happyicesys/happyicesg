<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Persontag extends Model
{
    protected $fillable = [
        'name'
    ];

    // relationships
    public function persontagattaches()
    {
        return $this->hasMany(Persontagattach::class);
    }

    // setter
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = str_replace(' ', '_', strtolower($value));
    }
}
