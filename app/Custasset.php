<?php

namespace App;

use Illuminate\Database\Eloquent\Model;



class Custasset extends Model
{
    protected $fillable = [
        'serial_number', 'sticker', 'condition', 'status',

        'personasset_id', 'dolocation_id'
    ];
}
