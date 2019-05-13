<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Persontagattach extends Model
{
    protected $fillable = [
        'person_id', 'persontag_id'
    ];
}
