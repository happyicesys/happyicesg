<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dolocation extends Model
{
/*
    status
    1 = active;
    99 = deactivated; */

    protected $fillable = [
        'location_name', 'attn_name', 'attn_phone_number', 'address', 'status',
        'postcode'
    ];
}
