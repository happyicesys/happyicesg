<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Postcode extends Model
{
    protected $fillable = [
        'value', 'block', 'remark', 'person_id', 'group_id'
    ];
    public function person()
    {
        return $this->belongsTo('App\Person');
    }

    public function group()
    {
        return $this->belongsTo('App\Group');
    }
}
