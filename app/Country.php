<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
        'name',
        'nationality_name',
        'currency_name',
        'currency_symbol',
        'phone_code',
        'is_city',
        'is_state',
    ];
}
