<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Postcode extends Model
{
    protected $fillable = [
        'value', 'block', 'area_code', 'area_name', 'group',
        'street',

        'person_id'
    ];
    public function person()
    {
        return $this->belongsTo('App\Person');
    }
}
