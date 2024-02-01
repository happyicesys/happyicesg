<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustPrefix extends Model
{
    protected $fillable = [
        'code',
        'desc',
    ];

    public function people()
    {
        return $this->hasMany('App\Person');
    }
}
