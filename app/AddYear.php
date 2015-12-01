<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AddYear extends Model
{
    protected $table = 'addyears';

    protected $fillable = [
        'name', 'logic'
    ];
}
