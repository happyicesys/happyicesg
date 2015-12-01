<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MinusMonth extends Model
{
    protected $table = 'minusmonths';

    protected $fillable = [
        'name', 'logic'
    ];
}
