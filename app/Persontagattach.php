<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Persontagattach extends Model
{
    protected $fillable = [
        'person_id', 'persontag_id'
    ];

    // relationships
    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function persontag()
    {
        return $this->belongsTo(Persontag::class);
    }
}
