<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Freezer extends Model
{
    protected $fillable = [
        'name'
    ];

    public function people()
    {
        return $this->belongsToMany('App\Person');
    }    
}
