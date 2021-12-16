<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vmhistory extends Model
{
    protected $fillable = [
        'vending_id', 'person_id', 'binding_date'
    ];

    public function vendings()
    {
        return $this->belongsToMany('App\Vending');
    }

    public function people()
    {
        return $this->belongsToMany('App\Person');
    }
}
