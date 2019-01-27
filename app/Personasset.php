<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Personasset extends Model
{
    protected $fillable = [
        'code', 'name', 'brand', 'size1', 'size2',
        'weight', 'capacity', 'specs1', 'specs2', 'specs3',
        'person_id', 'created_by'
    ];

    public function person()
    {
        return $this->belongsTo('App\Person');
    }

    public function transactionpersonassets()
    {
        return $this->hasMany('App\Transactionpersonasset');
    }
}
