<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AddMonth extends Model
{
    protected $table = 'addmonths';

    protected $fillable = [
        'name', 'logic'
    ];
}
