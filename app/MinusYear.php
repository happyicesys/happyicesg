<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MinusYear extends Model
{
    protected $table = 'minusyears';

    protected $fillable = [
        'name', 'logic'
    ];
}
