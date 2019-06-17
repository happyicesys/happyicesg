<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Custmovement extends Model
{
    protected $fillable = [
        'date', 'attn_name', 'attn_phone_number', 'address', 'status'
    ];
}
