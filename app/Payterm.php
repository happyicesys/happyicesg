<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payterm extends Model
{
    protected $fillable = [
        'name', 'desc'
    ];

    public function people()
    {
        return $this->hasMany('App\Person');
    }

}
